<?php

namespace App\Notifications;

use App\Models\FeePayment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FeeReceiptNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected FeePayment $payment;

    public function __construct(FeePayment $payment)
    {
        $this->payment = $payment;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Fee Payment Receipt - ' . $this->payment->receipt_no)
            ->greeting('Dear ' . $this->payment->admission->student_name . ',')
            ->line('We have received your payment of $' . number_format($this->payment->amount_paid, 2) . '.')
            ->line('Receipt Number: ' . $this->payment->receipt_no)
            ->line('Payment Method: ' . $this->payment->payment_method)
            ->line('Receipt Date: ' . $this->payment->receipt_date->format('Y-m-d'))
            ->line('Outstanding Fee Balance: $' . number_format($this->payment->admission->installments()->sum('due_amount'), 2))
            ->action('Download PDF Receipt', url('/admin/payments/' . $this->payment->id . '/receipt'))
            ->line('Thank you for studying with us!');
    }
}
