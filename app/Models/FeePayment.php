<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeePayment extends Model
{
    use HasUuids;

    protected $fillable = [
        'receipt_no',
        'admission_id',
        'fee_installment_id',
        'amount_paid',
        'payment_method',
        'transaction_reference',
        'receipt_date',
    ];

    protected $casts = [
        'receipt_date' => 'date',
        'amount_paid' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::creating(function ($payment) {
            if (empty($payment->receipt_no)) {
                $year = now()->year;
                $latest = self::where('receipt_no', 'like', "RCPT-{$year}-%")->latest('created_at')->first();
                if ($latest) {
                    $num = intval(substr($latest->receipt_no, -5)) + 1;
                } else {
                    $num = 1;
                }
                $payment->receipt_no = 'RCPT-' . $year . '-' . str_pad($num, 5, '0', STR_PAD_LEFT);
            }
        });

        static::created(function ($payment) {
            if ($payment->fee_installment_id) {
                $inst = $payment->installment;
                if ($inst) {
                    $inst->paid_amount += $payment->amount_paid;
                    $inst->due_amount = max(0.00, $inst->amount - $inst->paid_amount);
                    
                    if ($inst->due_amount <= 0) {
                        $inst->status = 'Paid';
                    } else {
                        $inst->status = 'Partial';
                    }
                    $inst->save();
                }
            }
        });
    }

    public function admission(): BelongsTo
    {
        return $this->belongsTo(Admission::class);
    }

    public function installment(): BelongsTo
    {
        return $this->belongsTo(FeeInstallment::class, 'fee_installment_id');
    }
}
