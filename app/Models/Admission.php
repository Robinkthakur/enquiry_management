<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
// use Spatie\Activitylog\Models\Concerns\LogsActivity;
// use Spatie\Activitylog\Support\LogOptions;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class Admission extends Model
{
    use HasUuids, SoftDeletes, Notifiable;

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
        'admission_date',
        'status',
        'user_id',
        'documents',
    ];

    protected $casts = [
        'admission_date' => 'date',
        'documents' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($admission) {
            $year = now()->year;
            $admission->admission_no = $admission->admission_no ?: 'ADM-' . $year . '-' . str_pad(self::count() + 1, 5, '0', STR_PAD_LEFT);
            $admission->roll_no = $admission->roll_no ?: 'ROLL-' . $year . '-' . str_pad(self::count() + 1, 5, '0', STR_PAD_LEFT);

            // Auto-create/Link Student User Account
            if (!empty($admission->email)) {
                $user = User::where('email', $admission->email)->first();
                if (!$user) {
                    $user = User::create([
                        'name' => $admission->student_name,
                        'email' => $admission->email,
                        'password' => Hash::make($admission->mobile),
                    ]);
                    $user->assignRole('Student');
                }
                $admission->user_id = $user->id;
            }
        });

        static::updating(function ($admission) {
            if (!$admission->user_id) {
                if (!empty($admission->email)) {
                    $user = User::where('email', $admission->email)->first();
                    if (!$user) {
                        $user = User::create([
                            'name' => $admission->student_name,
                            'email' => $admission->email,
                            'password' => Hash::make($admission->mobile),
                        ]);
                        $user->assignRole('Student');
                    }
                    $admission->user_id = $user->id;
                }
            } else {
                if ($admission->isDirty('email') || $admission->isDirty('student_name')) {
                    $user = User::find($admission->user_id);
                    if ($user) {
                        $user->update([
                            'email' => $admission->email,
                            'name' => $admission->student_name,
                        ]);
                    }
                }
            }
        });
    }

    // Accessors for Backwards Compatibility
    public function getFinalFeeAttribute(): float
    {
        return (float) $this->enrollments()->sum('final_fee');
    }

    public function getTotalFeeAttribute(): float
    {
        return (float) $this->enrollments()->sum('total_fee');
    }

    public function getDiscountAmountAttribute(): float
    {
        return (float) $this->enrollments()->sum('discount_amount');
    }

    public function getRegistrationFeeAttribute(): float
    {
        return (float) $this->enrollments()->sum('registration_fee');
    }

    public function getCourseAttribute()
    {
        $first = $this->enrollments()->first();
        return $first ? $first->course : null;
    }

    public function getTimeSlotAttribute()
    {
        $first = $this->enrollments()->first();
        return $first ? $first->time_slot : null;
    }

    public function getInstructorAttribute()
    {
        $first = $this->enrollments()->first();
        return $first ? $first->instructor : null;
    }

    public function getCourseIdAttribute()
    {
        $first = $this->enrollments()->first();
        return $first ? $first->course_id : null;
    }

    public function getInstructorIdAttribute()
    {
        $first = $this->enrollments()->first();
        return $first ? $first->instructor_id : null;
    }

    public function enquiry(): BelongsTo
    {
        return $this->belongsTo(Enquiry::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(AdmissionCourse::class, 'admission_id');
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'admission_courses', 'admission_id', 'course_id')
                    ->withPivot(['id', 'time_slot', 'instructor_id', 'total_fee', 'discount_amount', 'final_fee', 'registration_fee', 'status'])
                    ->withTimestamps();
    }

    public function instructor(): BelongsTo
    {
        // Keeping this for relations but we'll return instructor of the first course via accessor if accessed as property.
        // For query builders, whereHas('instructor') might fail or need custom handling.
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function leaveApplications(): HasMany
    {
        return $this->hasMany(LeaveApplication::class, 'admission_id');
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
