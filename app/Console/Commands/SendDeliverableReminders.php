<?php

namespace App\Console\Commands;

use App\Models\Deliverable;
use App\Models\Notification;
use App\Models\Tenant;
use Illuminate\Console\Command;

class SendDeliverableReminders extends Command
{
    protected $signature = 'projects:check-deliverables';
    protected $description = 'Check for overdue and upcoming deliverables and notify assigned users';

    public function handle(): int
    {
        $count = 0;

        Tenant::where('is_active', true)->each(function ($tenant) use (&$count) {
            // Overdue deliverables
            $overdue = Deliverable::where('tenant_id', $tenant->id)
                ->whereNotNull('due_date')
                ->where('due_date', '<', now())
                ->whereNotIn('status', ['approved', 'delivered'])
                ->with(['project', 'assignedTo'])
                ->get();

            foreach ($overdue as $del) {
                $userId = $del->assigned_to ?? $del->project?->project_manager_id;
                if (!$userId) continue;

                $existing = Notification::where('user_id', $userId)
                    ->where('type', 'task_overdue')
                    ->where('action_url', route('projects.show', $del->project_id))
                    ->whereDate('created_at', today())
                    ->exists();

                if ($existing) continue;

                Notification::create([
                    'tenant_id' => $tenant->id,
                    'user_id' => $userId,
                    'type' => 'task_overdue',
                    'title' => 'Overdue deliverable',
                    'message' => "\"{$del->title}\" on {$del->project->name} was due {$del->due_date->format('M j')}.",
                    'action_url' => route('projects.show', $del->project_id),
                    'icon' => 'alert',
                ]);

                $count++;
            }

            // Upcoming deliverables (due within 3 days)
            $upcoming = Deliverable::where('tenant_id', $tenant->id)
                ->whereNotNull('due_date')
                ->where('due_date', '>=', now())
                ->where('due_date', '<=', now()->addDays(3))
                ->whereNotIn('status', ['approved', 'delivered'])
                ->with(['project', 'assignedTo'])
                ->get();

            foreach ($upcoming as $del) {
                $userId = $del->assigned_to ?? $del->project?->project_manager_id;
                if (!$userId) continue;

                $existing = Notification::where('user_id', $userId)
                    ->where('type', 'task_upcoming')
                    ->where('action_url', route('projects.show', $del->project_id))
                    ->whereDate('created_at', today())
                    ->exists();

                if ($existing) continue;

                $days = (int) abs(now()->diffInDays($del->due_date));
                Notification::create([
                    'tenant_id' => $tenant->id,
                    'user_id' => $userId,
                    'type' => 'task_upcoming',
                    'title' => "Deliverable due in {$days} day(s)",
                    'message' => "\"{$del->title}\" on {$del->project->name} is due {$del->due_date->format('M j')}.",
                    'action_url' => route('projects.show', $del->project_id),
                    'icon' => 'clock',
                ]);

                $count++;
            }
        });

        $this->info("Processed {$count} deliverable reminders.");
        return Command::SUCCESS;
    }
}
