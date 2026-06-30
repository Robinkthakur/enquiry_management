<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasUuids;

    protected $fillable = [
        'admission_id',
        'admission_course_id',
        'attendance_date',
        'status',
    ];

    protected $casts = [
        'attendance_date' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Admission::class, 'admission_id');
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(AdmissionCourse::class, 'admission_course_id');
    }
}
