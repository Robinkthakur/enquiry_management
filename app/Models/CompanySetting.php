<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    protected $fillable = [
        'company_name',
        'description',
        'support_email',
        'mobile_no',
        'logo',
        'address',
        'website',
    ];
}
