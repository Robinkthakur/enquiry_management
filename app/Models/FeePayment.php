<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\FeeInstallment;

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
        'status',
        'screenshot',
    ];

    protected $casts = [
        'receipt_date' => 'date',
        'amount_paid' => 'decimal:2',
    ];

    public static function recalculateInstallment(string $installmentId): void
    {
        $inst = FeeInstallment::find($installmentId);
        if ($inst) {
            $totalPaid = self::where('fee_installment_id', $installmentId)->where('status', 'Verified')->sum('amount_paid');
            $inst->paid_amount = $totalPaid;
            $inst->due_amount = max(0.00, $inst->amount - $totalPaid);
            if ($inst->due_amount <= 0) {
                $inst->status = 'Paid';
            } elseif ($inst->paid_amount > 0) {
                $inst->status = 'Partial';
            } else {
                $inst->status = 'Pending';
            }
            $inst->save();
        }
    }

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

        static::saving(function ($payment) {
            if (empty($payment->fee_installment_id)) {
                if ($payment->admission_id) {
                    $installment = FeeInstallment::where('admission_id', $payment->admission_id)
                        ->where('due_amount', '>', 0)
                        ->orderBy('due_date', 'asc')
                        ->orderBy('installment_no', 'asc')
                        ->first();
                    if ($installment) {
                        $payment->fee_installment_id = $installment->id;
                    } else {
                        // fallback to first installment if none are pending
                        $firstInst = FeeInstallment::where('admission_id', $payment->admission_id)
                            ->orderBy('installment_no', 'asc')
                            ->first();
                        if ($firstInst) {
                            $payment->fee_installment_id = $firstInst->id;
                        }
                    }
                }
            }
        });

        static::created(function ($payment) {
            if ($payment->fee_installment_id) {
                self::recalculateInstallment($payment->fee_installment_id);
            }
        });

        static::updated(function ($payment) {
            if ($payment->fee_installment_id) {
                self::recalculateInstallment($payment->fee_installment_id);
            }
            if ($payment->isDirty('fee_installment_id') && $payment->getOriginal('fee_installment_id')) {
                self::recalculateInstallment($payment->getOriginal('fee_installment_id'));
            }
        });

        static::deleted(function ($payment) {
            if ($payment->fee_installment_id) {
                self::recalculateInstallment($payment->fee_installment_id);
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
