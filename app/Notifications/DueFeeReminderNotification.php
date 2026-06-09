<?php

namespace App\Notifications;

use App\Models\FeeInstallment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class DueFeeReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected FeeInstallment $installment;
    protected string $type; // e.g. 7_days_before, 3_days_before, on_due_date, overdue

    public function __construct(FeeInstallment $installment, string $type)
    {
        $this->installment = $installment;
        $this->type = $type;
    }

    public function via($notifiable): array
    {
        // Custom channels can be added, logging WhatsApp for demonstration
        $this->sendMockWhatsApp($notifiable);
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $admission = $this->installment->admission;
        $daysMessage = match($this->type) {
            '7_days_before' => 'is due in 7 days.',
            '3_days_before' => 'is due in 3 days.',
            'on_due_date' => 'is due today.',
            'overdue' => 'is OVERDUE. Please settle it immediately.',
            default => 'is due.',
        };

        $subject = 'Due Fee Reminder: Installment #' . $this->installment->installment_no;
        if ($this->type === 'overdue') {
            $subject = 'URGENT: Overdue Fee Reminder';
        }

        return (new MailMessage)
            ->subject($subject)
            ->greeting('Dear ' . $admission->student_name . ',')
            ->line('This is a reminder that installment #' . $this->installment->installment_no . ' for your course ' . $admission->course->course_name . ' ' . $daysMessage)
            ->line('Due Date: ' . $this->installment->due_date->format('Y-m-d'))
            ->line('Amount: $' . number_format($this->installment->due_amount, 2))
            ->action('Make Payment', url('/admin/admissions/' . $admission->id))
            ->line('Please ignore this email if you have already made the payment.');
    }

    protected function sendMockWhatsApp($notifiable): void
    {
        $admission = $this->installment->admission;
        $message = "Hello {$admission->student_name}, this is a reminder that installment #{$this->installment->installment_no} of amount \${$this->installment->due_amount} for {$admission->course->course_code} is due on {$this->installment->due_date->format('Y-m-d')}.";
        
        Log::info("WhatsApp SMS queued/sent to {$admission->mobile}: {$message}");
    }
}
