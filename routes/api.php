<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StudentApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public Authentication Routes
Route::post('/login', [StudentApiController::class, 'login']);
Route::post('/register', [StudentApiController::class, 'register']);
Route::post('/password/reset', [StudentApiController::class, 'resetPassword']);

// Private student photo temporary signed route
Route::get('/student-photo/{path}', [StudentApiController::class, 'getStudentPhoto'])
    ->where('path', '.*')
    ->name('api.student-photo')
    ->middleware('signed');

// Authenticated Student API Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [StudentApiController::class, 'logout']);
    Route::post('/password/change', [StudentApiController::class, 'changePassword']);
    
    // Student Profile Settings
    Route::get('/profile', [StudentApiController::class, 'profile']);
    Route::post('/profile/update', [StudentApiController::class, 'updateProfile']);
    
    // Courses & Self Enrollment
    Route::get('/courses', [StudentApiController::class, 'courses']);
    Route::get('/enrollments', [StudentApiController::class, 'enrollments']);
    Route::get('/enrollments/{id}', [StudentApiController::class, 'enrollmentDetails']);
    Route::post('/enrollments', [StudentApiController::class, 'enroll']);
    
    // Attendance Metrics & History Logs
    Route::get('/attendance', [StudentApiController::class, 'attendance']);
    Route::get('/attendance/stats', [StudentApiController::class, 'attendanceStats']);
    
    // Leave Application Management
    Route::get('/leaves', [StudentApiController::class, 'leaves']);
    Route::post('/leaves', [StudentApiController::class, 'applyLeave']);
    
    // Installments & Pay via QR Code Uploads
    Route::get('/installments', [StudentApiController::class, 'installments']);
    Route::post('/payments/pay-qr', [StudentApiController::class, 'payQR']);
    Route::get('/payments/{payment}/receipt', [StudentApiController::class, 'downloadReceipt']);

    // FCM Tokens & Push Notifications
    Route::post('/fcm-token', [StudentApiController::class, 'storeFcmToken']);
    Route::delete('/fcm-token', [StudentApiController::class, 'revokeFcmToken']);
    Route::post('/fcm-token/test-send', [StudentApiController::class, 'testSendNotification']);

    // Notifications Log & Status API
    Route::get('/notifications', [StudentApiController::class, 'getNotifications']);
    Route::post('/notifications/{id}/read', [StudentApiController::class, 'markNotificationAsRead']);
    Route::post('/notifications/read-all', [StudentApiController::class, 'markAllNotificationsAsRead']);
});
