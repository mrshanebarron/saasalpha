<?php

namespace App\Console\Commands;

use App\Models\CpdRecord;
use App\Models\Notification;
use App\Models\Tenant;
use Illuminate\Console\Command;

class SendCpdReminders extends Command
{
    protected $signature = 'cpd:send-reminders';
    protected $description = 'Send reminders for expiring CPD records and low CPD hours';

    public function handle(): int
    {
        $count = 0;

        Tenant::where('is_active', true)->each(function ($tenant) use (&$count) {
            // Check for expiring CPD records (within 30 days)
            $expiringRecords = CpdRecord::where('tenant_id', $tenant->id)
                ->whereNotNull('expiry_date')
                ->where('expiry_date', '>', now())
                ->where('expiry_date', '<=', now()->addDays(30))
                ->with('user')
                ->get();

            foreach ($expiringRecords as $record) {
                $days = (int) abs($record->expiry_date->diffInDays(now()));

                $existing = Notification::where('user_id', $record->user_id)
                    ->where('type', 'cpd_reminder')
                    ->where('title', 'LIKE', 'CPD record expiring%')
                    ->whereDate('created_at', today())
                    ->exists();

                if ($existing) continue;

                Notification::create([
                    'tenant_id' => $tenant->id,
                    'user_id' => $record->user_id,
                    'type' => 'cpd_reminder',
                    'title' => "CPD record expiring in {$days} days",
                    'message' => "\"{$record->title}\" expires on {$record->expiry_date->format('M j, Y')}. Consider renewing.",
                    'action_url' => route('cpd.edit', $record),
                    'icon' => 'book',
                ]);

                $count++;
            }

            // Check users with low CPD hours this year
            $tenant->users()->where('is_active', true)->each(function ($user) use ($tenant, &$count) {
                $yearHours = CpdRecord::where('user_id', $user->id)
                    ->whereYear('completed_date', now()->year)
                    ->sum('hours');

                // If under 10 hours by mid-year, send a nudge
                if (now()->month >= 6 && $yearHours < 10) {
                    $existing = Notification::where('user_id', $user->id)
                        ->where('type', 'cpd_reminder')
                        ->where('title', 'CPD hours reminder')
                        ->whereMonth('created_at', now()->month)
                        ->exists();

                    if ($existing) return;

                    Notification::create([
                        'tenant_id' => $tenant->id,
                        'user_id' => $user->id,
                        'type' => 'cpd_reminder',
                        'title' => 'CPD hours reminder',
                        'message' => "You have {$yearHours} CPD hours this year. Consider logging additional professional development.",
                        'action_url' => route('cpd.index'),
                        'icon' => 'book',
                    ]);

                    $count++;
                }
            });
        });

        $this->info("Sent {$count} CPD reminders.");
        return Command::SUCCESS;
    }
}
