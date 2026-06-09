<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Course extends Model
{
    use HasUuids, SoftDeletes, LogsActivity;

    protected $fillable = [
        'course_code',
        'course_name',
        'description',
        'duration_months',
        'total_fee',
        'registration_fee',
        'certificate_fee',
        'tax_percentage',
        'status',
    ];

    protected $casts = [
        'duration_months' => 'integer',
        'total_fee' => 'decimal:2',
        'registration_fee' => 'decimal:2',
        'certificate_fee' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    public function enquiries(): BelongsToMany
    {
        return $this->belongsToMany(Enquiry::class, 'enquiry_courses');
    }

    public function batches(): HasMany
    {
        return $this->hasMany(Batch::class);
    }

    public function admissions(): HasMany
    {
        return $this->hasMany(Admission::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }
}
