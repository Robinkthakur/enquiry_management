<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeeHold extends Model
{
    use HasUuids;

    protected $fillable = [
        'admission_id',
        'hold_from',
        'hold_to',
        'reason',
        'approved_by',
    ];

    protected $casts = [
        'hold_from' => 'date',
        'hold_to' => 'date',
    ];

    public function admission(): BelongsTo
    {
        return $this->belongsTo(Admission::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
