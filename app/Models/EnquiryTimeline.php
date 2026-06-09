<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnquiryTimeline extends Model
{
    use HasUuids;

    protected $table = 'enquiry_timeline';

    protected $fillable = [
        'enquiry_id',
        'user_id',
        'status_from',
        'status_to',
        'notes',
        'follow_up_date',
    ];

    protected $casts = [
        'follow_up_date' => 'date',
    ];

    public function enquiry(): BelongsTo
    {
        return $this->belongsTo(Enquiry::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
