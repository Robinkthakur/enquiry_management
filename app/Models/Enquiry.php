<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Enquiry extends Model
{
    use HasUuids, SoftDeletes, LogsActivity;

    protected $fillable = [
        'enquiry_no',
        'name',
        'father_name',
        'mobile',
        'email',
        'gender',
        'date_of_birth',
        'qualification',
        'occupation',
        'address',
        'enquiry_source',
        'remarks',
        'follow_up_date',
        'taken_by',
        'status',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'follow_up_date' => 'date',
    ];

    protected static function booted()
    {
        static::creating(function ($enquiry) {
            if (empty($enquiry->enquiry_no)) {
                $year = now()->year;
                $latest = self::where('enquiry_no', 'like', "ENQ-{$year}-%")->latest('created_at')->first();
                if ($latest) {
                    $num = intval(substr($latest->enquiry_no, -5)) + 1;
                } else {
                    $num = 1;
                }
                $enquiry->enquiry_no = 'ENQ-' . $year . '-' . str_pad($num, 5, '0', STR_PAD_LEFT);
            }
        });

        static::created(function ($enquiry) {
            $enquiry->timeline()->create([
                'user_id' => auth()->id() ?? $enquiry->taken_by ?? User::first()->id,
                'status_from' => null,
                'status_to' => $enquiry->status,
                'notes' => 'Enquiry registered. Remarks: ' . $enquiry->remarks,
                'follow_up_date' => $enquiry->follow_up_date,
            ]);
        });

        static::updating(function ($enquiry) {
            if ($enquiry->isDirty('status') || $enquiry->isDirty('follow_up_date') || $enquiry->isDirty('remarks')) {
                $notes = [];
                if ($enquiry->isDirty('status')) {
                    $notes[] = 'Status changed from ' . $enquiry->getOriginal('status') . ' to ' . $enquiry->status;
                }
                if ($enquiry->isDirty('follow_up_date')) {
                    $notes[] = 'Follow-up date changed to ' . ($enquiry->follow_up_date ? $enquiry->follow_up_date->format('Y-m-d') : 'None');
                }
                if ($enquiry->isDirty('remarks') && !$enquiry->isDirty('status')) {
                    $notes[] = 'Remarks updated';
                }

                $enquiry->timeline()->create([
                    'user_id' => auth()->id() ?? User::first()->id,
                    'status_from' => $enquiry->getOriginal('status'),
                    'status_to' => $enquiry->status,
                    'notes' => implode(', ', $notes) . ($enquiry->isDirty('remarks') ? '. Remarks: ' . $enquiry->remarks : ''),
                    'follow_up_date' => $enquiry->follow_up_date,
                ]);
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

    public function interestedCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'enquiry_courses');
    }

    public function counselor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'taken_by');
    }

    public function timeline(): HasMany
    {
        return $this->hasMany(EnquiryTimeline::class)->orderBy('created_at', 'desc');
    }

    public function admission(): HasOne
    {
        return $this->hasOne(Admission::class);
    }
}
