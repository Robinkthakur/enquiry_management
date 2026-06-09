<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\CompanySetting;
use Illuminate\Http\Request;

class CertificateVerificationController extends Controller
{
    /**
     * Handle public verification requests for student certificates.
     */
    public function verify($token)
    {
        $certificate = Certificate::with(['admission', 'course'])
            ->where('verification_token', $token)
            ->first();

        $setting = CompanySetting::first();

        return view('verify-certificate', compact('certificate', 'setting'));
    }
}
