<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class AdmissionCourse extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'admission_courses';

    protected $fillable = [
        'admission_id',
        'course_id',
        'time_slot',
        'instructor_id',
        'total_fee',
        'discount_amount',
        'final_fee',
        'registration_fee',
        'status',
    ];

    protected $casts = [
        'total_fee' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_fee' => 'decimal:2',
        'registration_fee' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::created(function ($enrollment) {
            if ($enrollment->installments()->count() === 0) {
                $enrollment->installments()->create([
                    'admission_id' => $enrollment->admission_id,
                    'installment_no' => 1,
                    'due_date' => $enrollment->admission->admission_date ?? now(),
                    'amount' => $enrollment->final_fee,
                    'paid_amount' => 0.00,
                    'due_amount' => $enrollment->final_fee,
                    'status' => 'Pending',
                ]);
            }
        });

        static::updated(function ($enrollment) {
            if ($enrollment->wasChanged(['final_fee'])) {
                $installment = $enrollment->installments()->first();
                if ($installment) {
                    $installment->amount = $enrollment->final_fee;
                    // Recalculate based on existing payments
                    $totalPaid = $enrollment->payments()->sum('amount_paid');
                    $installment->paid_amount = $totalPaid;
                    $installment->due_amount = max(0.00, $enrollment->final_fee - $totalPaid);
                    if ($installment->due_amount <= 0) {
                        $installment->status = 'Paid';
                    } elseif ($installment->paid_amount > 0) {
                        $installment->status = 'Partial';
                    } else {
                        $installment->status = 'Pending';
                    }
                    $installment->save();
                }
            }
        });
    }

    public function getStudentNameAttribute(): string
    {
        return $this->admission->student_name ?? '';
    }

    public function getRollNoAttribute(): string
    {
        return $this->admission->roll_no ?? '';
    }

    public function getAdmissionNoAttribute(): string
    {
        return $this->admission->admission_no ?? '';
    }

    public function getStudentPhotoAttribute()
    {
        return $this->admission->student_photo ?? null;
    }

    public function admission(): BelongsTo
    {
        return $this->belongsTo(Admission::class, 'admission_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function installments(): HasMany
    {
        return $this->hasMany(FeeInstallment::class, 'admission_course_id');
    }

    public function payments(): HasManyThrough
    {
        return $this->hasManyThrough(
            FeePayment::class,
            FeeInstallment::class,
            'admission_course_id', // Foreign key on fee_installments table
            'fee_installment_id', // Foreign key on fee_payments table
            'id',                  // Local key on admission_courses table
            'id'                   // Local key on fee_installments table
        );
    }
}
