<?php

use App\Http\Controllers\PDFController;
use App\Http\Controllers\CertificateVerificationController;

Route::get('/', function () {
    return redirect('admin');
});

// PDF Downloads (authenticated users only)
Route::get('/admin/payments/{payment}/receipt', [PDFController::class, 'downloadReceipt'])
    ->middleware('auth:web,admin')
    ->name('admin.payments.receipt');

Route::get('/admin/certificates/{certificate}/pdf', [PDFController::class, 'downloadCertificate'])
    ->middleware('auth:web,admin')
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
})->where('path', '.*')->middleware('auth:web,admin')->name('admin.student-photo');

// Serve student documents from private local disk (authenticated users only)
Route::get('/admin/student-documents/{path}', function (string $path) {
    if (! \Illuminate\Support\Facades\Storage::disk('local')->exists($path)) {
        abort(404);
    }
    return \Illuminate\Support\Facades\Storage::disk('local')->response($path);
})->where('path', '.*')->middleware('auth:web,admin')->name('admin.student-document');

// Serve payment screenshots from private local disk (authenticated users only)
Route::get('/admin/payment-screenshots/{path}', function (string $path) {
    if (! \Illuminate\Support\Facades\Storage::disk('local')->exists($path)) {
        abort(404);
    }
    return \Illuminate\Support\Facades\Storage::disk('local')->response($path);
})->where('path', '.*')->middleware('auth:web,admin')->name('admin.payment-screenshot');

