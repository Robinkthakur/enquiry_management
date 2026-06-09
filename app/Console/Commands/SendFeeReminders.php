<?php

namespace App\Console\Commands;

use App\Models\FeeInstallment;
use App\Notifications\DueFeeReminderNotification;
use Illuminate\Console\Command;

class SendFeeReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-fee-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan fee installments and dispatch due reminders to students';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Starting fee reminder scan...');

        $today = now()->toDateString();
        $date7DaysLater = now()->addDays(7)->toDateString();
        $date3DaysLater = now()->addDays(3)->toDateString();

        // 1. Fetch unpaid installments
        $installments = FeeInstallment::whereIn('status', ['Pending', 'Partial', 'Overdue'])
            ->with(['admission.course', 'admission.holds'])
            ->get();

        $dispatchedCount = 0;

        foreach ($installments as $inst) {
            $student = $inst->admission;

            // Skip if student fee reminders are on hold
            if ($student->hasActiveHold()) {
                $this->info("Skipping student {$student->student_name} (Fee reminders are on hold).");
                continue;
            }

            $dueDate = $inst->due_date->toDateString();
            $type = null;

            if ($dueDate === $date7DaysLater) {
                $type = '7_days_before';
            } elseif ($dueDate === $date3DaysLater) {
                $type = '3_days_before';
            } elseif ($dueDate === $today) {
                $type = 'on_due_date';
            } elseif ($dueDate < $today) {
                $type = 'overdue';
                
                // Update installment status to Overdue if it is still Pending/Partial
                if ($inst->status !== 'Overdue') {
                    $inst->update(['status' => 'Overdue']);
                }
            }

            if ($type) {
                $student->notify(new DueFeeReminderNotification($inst, $type));
                $dispatchedCount++;
                $this->info("Reminder queued for {$student->student_name} (Installment #{$inst->installment_no}, Type: {$type})");
            }
        }

        $this->info("Scan completed. Dispatched {$dispatchedCount} reminders.");
    }
}
