<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    use HasUuids;

    protected $fillable = [
        'certificate_no',
        'admission_id',
        'course_id',
        'issue_date',
        'completion_date',
        'verification_token',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'completion_date' => 'date',
    ];

    protected static function booted()
    {
        static::creating(function ($certificate) {
            if (empty($certificate->certificate_no)) {
                $year = now()->year;
                $latest = self::where('certificate_no', 'like', "CERT-{$year}-%")->latest('created_at')->first();
                if ($latest) {
                    $num = intval(substr($latest->certificate_no, -5)) + 1;
                } else {
                    $num = 1;
                }
                $certificate->certificate_no = 'CERT-' . $year . '-' . str_pad($num, 5, '0', STR_PAD_LEFT);
            }
            if (empty($certificate->verification_token)) {
                $certificate->verification_token = \Illuminate\Support\Str::random(32);
            }
        });
    }

    public function admission(): BelongsTo
    {
        return $this->belongsTo(Admission::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
