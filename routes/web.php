<?php

use App\Http\Controllers\PDFController;
use App\Http\Controllers\CertificateVerificationController;

Route::get('/', function () {
    return redirect('admin');
});

// PDF Downloads (authenticated users only)
Route::get('/admin/payments/{payment}/receipt', [PDFController::class, 'downloadReceipt'])
    ->middleware('auth')
    ->name('admin.payments.receipt');

Route::get('/admin/certificates/{certificate}/pdf', [PDFController::class, 'downloadCertificate'])
    ->middleware('auth')
    ->name('admin.certificates.pdf');

// Public Certificate Verification URL
Route::get('/verify-certificate/{token}', [CertificateVerificationController::class, 'verify'])
    ->name('certificates.verify');

// Serve student photos from private local disk (authenticated users only)
Route::get('/admin/student-photos/{path}', function (string $path) {
    if (! \Illuminate\Support\Facades\Storage::disk('local')->exists($path)) {
        abort(404);
    }
    return \Illuminate\Support\Facades\Storage::disk('local')->response($path);
})->where('path', '.*')->middleware('auth')->name('admin.student-photo');
