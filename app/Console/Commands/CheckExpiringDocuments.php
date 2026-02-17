<?php

namespace App\Console\Commands;

use App\Models\ComplianceDocument;
use App\Models\Notification;
use App\Models\Tenant;
use Illuminate\Console\Command;

class CheckExpiringDocuments extends Command
{
    protected $signature = 'compliance:check-expiring';
    protected $description = 'Check for expiring compliance documents and create notifications';

    public function handle(): int
    {
        $count = 0;

        Tenant::where('is_active', true)->each(function ($tenant) use (&$count) {
            $expiringDocs = ComplianceDocument::where('tenant_id', $tenant->id)
                ->whereNotNull('expiry_date')
                ->where('expiry_date', '>', now())
                ->whereRaw('expiry_date <= DATE_ADD(NOW(), INTERVAL reminder_days DAY)')
                ->get();

            foreach ($expiringDocs as $doc) {
                $days = (int) abs($doc->expiry_date->diffInDays(now()));

                // Don't duplicate notifications — check if one was created today for this doc
                $existing = Notification::where('tenant_id', $tenant->id)
                    ->where('type', 'expiry_warning')
                    ->where('action_url', route('compliance.show', $doc))
                    ->whereDate('created_at', today())
                    ->exists();

                if ($existing) continue;

                // Notify all admins and managers in the tenant
                $tenant->users()
                    ->whereIn('role', ['admin', 'manager'])
                    ->where('is_active', true)
                    ->each(function ($user) use ($doc, $days, $tenant) {
                        Notification::create([
                            'tenant_id' => $tenant->id,
                            'user_id' => $user->id,
                            'type' => 'expiry_warning',
                            'title' => "Document expiring in {$days} days",
                            'message' => "{$doc->title} ({$doc->type}) expires on {$doc->expiry_date->format('M j, Y')}.",
                            'action_url' => route('compliance.show', $doc),
                            'icon' => 'shield',
                        ]);
                    });

                $count++;
            }

            // Also check already expired docs
            $expiredDocs = ComplianceDocument::where('tenant_id', $tenant->id)
                ->whereNotNull('expiry_date')
                ->where('expiry_date', '<', now())
                ->get();

            foreach ($expiredDocs as $doc) {
                $existing = Notification::where('tenant_id', $tenant->id)
                    ->where('type', 'expiry_warning')
                    ->where('title', 'LIKE', 'Document expired%')
                    ->where('action_url', route('compliance.show', $doc))
                    ->whereDate('created_at', today())
                    ->exists();

                if ($existing) continue;

                $tenant->users()
                    ->whereIn('role', ['admin', 'manager'])
                    ->where('is_active', true)
                    ->each(function ($user) use ($doc, $tenant) {
                        Notification::create([
                            'tenant_id' => $tenant->id,
                            'user_id' => $user->id,
                            'type' => 'expiry_warning',
                            'title' => 'Document expired',
                            'message' => "{$doc->title} ({$doc->type}) expired on {$doc->expiry_date->format('M j, Y')}. Immediate action required.",
                            'action_url' => route('compliance.show', $doc),
                            'icon' => 'shield',
                        ]);
                    });

                $count++;
            }
        });

        $this->info("Processed {$count} expiring/expired documents.");
        return Command::SUCCESS;
    }
}
