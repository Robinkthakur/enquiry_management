<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class Admission extends Model
{
    use HasUuids, SoftDeletes, LogsActivity, Notifiable;

    protected $fillable = [
        'admission_no',
        'enquiry_id',
        'student_photo',
        'roll_no',
        'student_name',
        'father_name',
        'mobile',
        'email',
        'address',
        'course_id',
        'time_slot',
        'instructor_id',
        'admission_date',
        'total_fee',
        'discount_amount',
        'final_fee',
        'registration_fee',
        'status',
    ];

    protected $casts = [
        'admission_date' => 'date',
        'total_fee' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_fee' => 'decimal:2',
        'registration_fee' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::creating(function ($admission) {
            $year = now()->year;
            
            // Generate Admission No
            if (empty($admission->admission_no)) {
                $latestAdm = self::where('admission_no', 'like', "ADM-{$year}-%")->latest('created_at')->first();
                if ($latestAdm) {
                    $numAdm = intval(substr($latestAdm->admission_no, -5)) + 1;
                } else {
                    $numAdm = 1;
                }
                $admission->admission_no = 'ADM-' . $year . '-' . str_pad($numAdm, 5, '0', STR_PAD_LEFT);
            }

            // Generate Roll No
            if (empty($admission->roll_no)) {
                $latestRoll = self::where('roll_no', 'like', "ROLL-{$year}-%")->latest('created_at')->first();
                if ($latestRoll) {
                    $numRoll = intval(substr($latestRoll->roll_no, -5)) + 1;
                } else {
                    $numRoll = 1;
                }
                $admission->roll_no = 'ROLL-' . $year . '-' . str_pad($numRoll, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    public function enquiry(): BelongsTo
    {
        return $this->belongsTo(Enquiry::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }


    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function installments(): HasMany
    {
        return $this->hasMany(FeeInstallment::class)->orderBy('installment_no', 'asc');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(FeePayment::class)->orderBy('receipt_date', 'desc');
    }

    public function holds(): HasMany
    {
        return $this->hasMany(FeeHold::class)->orderBy('created_at', 'desc');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class)->orderBy('attendance_date', 'desc');
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    /**
     * Check if student fee reminders are currently paused.
     */
    public function hasActiveHold(): bool
    {
        $today = now()->toDateString();
        return $this->holds()
            ->where('hold_from', '<=', $today)
            ->where(function ($query) use ($today) {
                $query->whereNull('hold_to')
                      ->orWhere('hold_to', '>=', $today);
            })
            ->exists();
    }

    /**
     * Get attendance percentage for the student.
     */
    public function getAttendancePercentageAttribute(): float
    {
        $total = $this->attendances()->count();
        if ($total === 0) {
            return 0.0;
        }
        $present = $this->attendances()->whereIn('status', ['Present', 'Leave'])->count();
        return round(($present / $total) * 100, 2);
    }
}
