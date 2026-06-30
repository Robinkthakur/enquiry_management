<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PDFController;
use App\Models\User;
use App\Models\Course;
use App\Models\AdmissionCourse;
use App\Models\Attendance;
use App\Models\LeaveApplication;
use App\Models\FeeInstallment;
use App\Models\FeePayment;
use App\Models\FcmToken;
use App\Models\Notification;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\URL;

class StudentApiController extends Controller
{
    /**
     * Register a new student account.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:admissions,email|unique:users,email',
            'phone' => 'required|string|max:50',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Create the Admission record
        // The booted hook will automatically generate admission_no, roll_no, create the User with role Student, and set user_id
        $admission = \App\Models\Admission::create([
            'student_name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->phone,
            'admission_date' => now(),
            'status' => 'Active',
        ]);

        // Retrieve the auto-created student user and update password
        $user = $admission->user;
        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        // Generate Sanctum access token for the user
        $token = $user->createToken('student-mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Account created successfully.',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'student' => [
                'id' => $admission->id,
                'admission_no' => $admission->admission_no,
                'student_name' => $admission->student_name,
                'mobile' => $admission->mobile,
                'email' => $admission->email,
                'photo_url' => $admission->student_photo ? URL::temporarySignedRoute('api.student-photo', now()->addHours(5), ['path' => $admission->student_photo]) : null,
            ]
        ], 201);
    }

    /**
     * Authenticate student and issue API token.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $admission = \App\Models\Admission::where('email', $request->email)
            ->orWhere('admission_no', $request->email)
            ->first();

        $user = null;
        if ($admission && $admission->user) {
            $user = $admission->user;
        } else {
            $user = \App\Models\User::where('email', $request->email)->first();
        }

        if (!$user) {
            // Check if the email belongs to an Admin user
            $isAdmin = \App\Models\Admin::where('email', $request->email)->exists();
            if ($isAdmin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Only student accounts can login.'
                ], 403);
            }

            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.'
            ], 401);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.'
            ], 401);
        }

        if (!$user->hasRole('Student')) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Only student accounts can login.'
            ], 403);
        }

    
        $token = $user->createToken('student-mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'student' => $admission ? [
                'id' => $admission->id,
                'admission_no' => $admission->admission_no,
                'student_name' => $admission->student_name,
                'mobile' => $admission->mobile,
                'email' => $admission->email,
                'photo_url' => $admission->student_photo ? URL::temporarySignedRoute('api.student-photo', now()->addHours(5), ['path' => $admission->student_photo]) : null,
            ] : null
        ]);
    }

    /**
     * Revoke current API token.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out.'
        ]);
    }

    /**
     * Retrieve student profile.
     */
    public function profile(Request $request)
    {
        $user = $request->user();
        $admission = $user->admission;

        if (!$admission) {
            return response()->json([
                'success' => false,
                'message' => 'Student record not found.'
            ], 404);
        }

        $documentUrls = [];
        if ($admission->documents) {
            foreach ($admission->documents as $doc) {
                $documentUrls[] = [
                    'filename' => basename($doc),
                    'url' => url('/admin/student-documents/' . $doc)
                ];
            }
        }

        return response()->json([
            'success' => true,
            'profile' => [
                'admission_no' => $admission->admission_no,
                'student_name' => $admission->student_name,
                'father_name' => $admission->father_name,
                'mobile' => $admission->mobile,
                'email' => $admission->email,
                'address' => $admission->address,
                'admission_date' => $admission->admission_date ? $admission->admission_date->toDateString() : null,
                'status' => $admission->status,
                'photo_url' => $admission->student_photo ? URL::temporarySignedRoute('api.student-photo', now()->addHours(5), ['path' => $admission->student_photo]) : null,
                'documents' => $documentUrls,
            ]
        ]);
    }

    /**
     * Update student email and photo.
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $admission = $user->admission;

        if (!$admission) {
            return response()->json([
                'success' => false,
                'message' => 'Student record not found.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'student_photo' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Update email on both models
        $user->update(['email' => $request->email]);
        $admission->email = $request->email;

        // Handle profile photo upload
        if ($request->hasFile('student_photo')) {
            // Delete old file
            if ($admission->student_photo && Storage::disk('local')->exists($admission->student_photo)) {
                Storage::disk('local')->delete($admission->student_photo);
            }

            $path = $request->file('student_photo')->store('student_photos', 'local');
            $admission->student_photo = $path;
        }

        $admission->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'profile' => [
                'email' => $admission->email,
                'photo_url' => $admission->student_photo ? URL::temporarySignedRoute('api.student-photo', now()->addHours(5), ['path' => $admission->student_photo]) : null,
            ]
        ]);
    }

    /**
     * List all active courses.
     */
    public function courses()
    {
        $courses = Course::where('status', 'active')->get()->map(function ($c) {
            return [
                'id' => $c->id,
                'course_code' => $c->course_code,
                'course_name' => $c->course_name,
                'description' => $c->description,
                'duration_months' => $c->duration_months,
                'total_fee' => (float) $c->total_fee,
                'registration_fee' => (float) $c->registration_fee,
            ];
        });

        return response()->json([
            'success' => true,
            'courses' => $courses
        ]);
    }

    /**
     * List current enrolled courses.
     */
    public function enrollments(Request $request)
    {
        $admission = $request->user()->admission;
        if (!$admission) {
            return response()->json(['success' => false, 'message' => 'Profile not found.'], 404);
        }

        $enrollments = $admission->enrollments()->with('course', 'instructor')->get()->map(function ($enroll) {
            return [
                'id' => $enroll->id,
                'course_code' => $enroll->course->course_code,
                'course_name' => $enroll->course->course_name,
                'time_slot' => $enroll->time_slot,
                'instructor_name' => $enroll->instructor ? $enroll->instructor->name : null,
                'final_fee' => (float) $enroll->final_fee,
                'status' => $enroll->status,
            ];
        });

        return response()->json([
            'success' => true,
            'enrollments' => $enrollments
        ]);
    }

    /**
     * View full details of a specific enrollment.
     */
    public function enrollmentDetails(Request $request, $id)
    {
        $admission = $request->user()->admission;
        if (!$admission) {
            return response()->json(['success' => false, 'message' => 'Profile not found.'], 404);
        }

        $enroll = AdmissionCourse::where('admission_id', $admission->id)
            ->where('id', $id)
            ->with(['course', 'instructor'])
            ->first();

        if (!$enroll) {
            return response()->json([
                'success' => false,
                'message' => 'Enrollment not found.'
            ], 404);
        }

        $installments = $enroll->installments()->orderBy('installment_no')->get()->map(function ($inst) {
            return [
                'id' => $inst->id,
                'installment_no' => $inst->installment_no,
                'due_date' => $inst->due_date ? $inst->due_date->toDateString() : null,
                'amount' => (float) $inst->amount,
                'paid_amount' => (float) $inst->paid_amount,
                'due_amount' => (float) $inst->due_amount,
                'status' => $inst->status,
            ];
        });

        $payments = $enroll->payments()->orderBy('receipt_date', 'desc')->get()->map(function ($pmt) {
            return [
                'id' => $pmt->id,
                'receipt_no' => $pmt->receipt_no,
                'amount_paid' => (float) $pmt->amount_paid,
                'payment_method' => $pmt->payment_method,
                'transaction_reference' => $pmt->transaction_reference,
                'receipt_date' => $pmt->receipt_date ? $pmt->receipt_date->toDateString() : null,
                'status' => $pmt->status,
                'screenshot_url' => $pmt->screenshot ? url('/admin/payment-screenshots/' . $pmt->screenshot) : null,
                'receipt_download_url' => $pmt->status === 'Verified' ? route('admin.payments.receipt', ['payment' => $pmt->id]) : null,
            ];
        });

        $totalPaid = $enroll->payments()->where('fee_payments.status', 'Verified')->sum('amount_paid');
        $totalPending = $enroll->payments()->where('fee_payments.status', 'Pending')->sum('amount_paid');
        $totalDue = max(0.00, $enroll->final_fee - $totalPaid);

        return response()->json([
            'success' => true,
            'details' => [
                'id' => $enroll->id,
                'course' => [
                    'course_code' => $enroll->course->course_code,
                    'course_name' => $enroll->course->course_name,
                    'description' => $enroll->course->description,
                    'duration_months' => $enroll->course->duration_months,
                    'standard_total_fee' => (float) $enroll->course->total_fee,
                    'standard_registration_fee' => (float) $enroll->course->registration_fee,
                ],
                'schedule' => [
                    'time_slot' => $enroll->time_slot,
                ],
                'instructor' => $enroll->instructor ? [
                    'name' => $enroll->instructor->name,
                    'email' => $enroll->instructor->email,
                ] : null,
                'financials' => [
                    'final_fee' => (float) $enroll->final_fee,
                    'registration_fee' => (float) $enroll->registration_fee,
                    'total_paid' => (float) $totalPaid,
                    'total_pending' => (float) $totalPending,
                    'total_due' => (float) $totalDue,
                ],
                'installments' => $installments,
                'payments' => $payments,
                'status' => $enroll->status,
            ]
        ]);
    }

    /**
     * Self enroll in a new course.
     */
    public function enroll(Request $request)
    {
        $admission = $request->user()->admission;
        if (!$admission) {
            return response()->json(['success' => false, 'message' => 'Profile not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id,status,active',
            'start_time' => 'required|string',
            'end_time' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $course = Course::find($request->course_id);

        // Check if already enrolled in this course
        $alreadyEnrolled = AdmissionCourse::where('admission_id', $admission->id)
            ->where('course_id', $request->course_id)
            ->exists();

        if ($alreadyEnrolled) {
            return response()->json([
                'success' => false,
                'message' => 'You are already enrolled in this course.'
            ], 422);
        }

        $timeSlot = $request->start_time . ' - ' . $request->end_time;

        $enroll = AdmissionCourse::create([
            'admission_id' => $admission->id,
            'course_id' => $course->id,
            'time_slot' => $timeSlot,
            'total_fee' => $course->total_fee,
            'discount_amount' => 0.00,
            'final_fee' => $course->total_fee,
            'registration_fee' => $course->registration_fee,
            'status' => 'Active',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Enrolled successfully.',
            'enrollment_id' => $enroll->id
        ], 201);
    }

    /**
     * List attendance logs.
     */
    public function attendance(Request $request)
    {
        $admission = $request->user()->admission;
        if (!$admission) {
            return response()->json(['success' => false, 'message' => 'Profile not found.'], 404);
        }

        $query = Attendance::where('admission_id', $admission->id)->with('enrollment.course');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('course_id')) {
            $query->whereHas('enrollment', function ($q) use ($request) {
                $q->where('course_id', $request->course_id);
            });
        }

        $logs = $query->orderBy('attendance_date', 'desc')->get()->map(function ($log) {
            return [
                'id' => $log->id,
                'date' => $log->attendance_date->toDateString(),
                'course_name' => $log->enrollment->course->course_name,
                'status' => $log->status,
            ];
        });

        return response()->json([
            'success' => true,
            'logs' => $logs
        ]);
    }

    /**
     * Retrieve attendance stats overview.
     */
    public function attendanceStats(Request $request)
    {
        $admission = $request->user()->admission;
        if (!$admission) {
            return response()->json(['success' => false, 'message' => 'Profile not found.'], 404);
        }

        $attendances = $admission->attendances;
        $total = $attendances->count();
        $present = $attendances->where('status', 'Present')->count();
        $absent = $attendances->where('status', 'Absent')->count();
        $leave = $attendances->where('status', 'Leave')->count();

        $percentage = $total > 0 ? round((($present + $leave) / $total) * 100, 1) : 0;

        return response()->json([
            'success' => true,
            'stats' => [
                'total_conducted' => $total,
                'present_days' => $present,
                'absent_days' => $absent,
                'leave_days' => $leave,
                'attendance_rate' => $percentage . '%'
            ]
        ]);
    }

    /**
     * List leave applications.
     */
    public function leaves(Request $request)
    {
        $admission = $request->user()->admission;
        if (!$admission) {
            return response()->json(['success' => false, 'message' => 'Profile not found.'], 404);
        }

        $leaves = $admission->leaveApplications()->orderBy('created_at', 'desc')->get()->map(function ($leave) {
            return [
                'id' => $leave->id,
                'start_date' => $leave->start_date->toDateString(),
                'end_date' => $leave->end_date->toDateString(),
                'reason' => $leave->reason,
                'status' => $leave->status,
                'admin_remarks' => $leave->admin_remarks,
            ];
        });

        return response()->json([
            'success' => true,
            'leaves' => $leaves
        ]);
    }

    /**
     * Submit leave application.
     */
    public function applyLeave(Request $request)
    {
        $admission = $request->user()->admission;
        if (!$admission) {
            return response()->json(['success' => false, 'message' => 'Profile not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Active leave application constraint check
        $exists = LeaveApplication::where('admission_id', $admission->id)
            ->whereIn('status', ['Pending', 'Approved'])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'You already have an active or pending leave application.'
            ], 422);
        }

        $leave = LeaveApplication::create([
            'admission_id' => $admission->id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'status' => 'Pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Leave application submitted successfully.',
            'leave_id' => $leave->id
        ], 201);
    }

    /**
     * List all fee installments.
     */
    public function installments(Request $request)
    {
        $admission = $request->user()->admission;
        if (!$admission) {
            return response()->json(['success' => false, 'message' => 'Profile not found.'], 404);
        }

        $installments = FeeInstallment::where('admission_id', $admission->id)
            ->with('enrollment.course')
            ->orderBy('due_date', 'asc')
            ->orderBy('installment_no', 'asc')
            ->get()
            ->map(function ($inst) {
                return [
                    'id' => $inst->id,
                    'installment_no' => $inst->installment_no,
                    'course_name' => $inst->enrollment->course->course_name,
                    'due_date' => $inst->due_date ? $inst->due_date->toDateString() : null,
                    'amount' => (float) $inst->amount,
                    'paid_amount' => (float) $inst->paid_amount,
                    'due_amount' => (float) $inst->due_amount,
                    'status' => $inst->status,
                ];
            });

        return response()->json([
            'success' => true,
            'installments' => $installments
        ]);
    }

    /**
     * Submit QR payment proof.
     */
    public function payQR(Request $request)
    {
        $admission = $request->user()->admission;
        if (!$admission) {
            return response()->json(['success' => false, 'message' => 'Profile not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'fee_installment_id' => [
                'required',
                'exists:fee_installments,id,admission_id,' . $admission->id,
            ],
            'amount_paid' => 'required|numeric|min:1',
            'payment_method' => 'required|string',
            'transaction_reference' => 'required|string|unique:fee_payments,transaction_reference',
            'receipt_date' => 'required|date',
            'screenshot' => 'required|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $screenshotPath = $request->file('screenshot')->store('payment_screenshots', 'local');

        $payment = FeePayment::create([
            'admission_id' => $admission->id,
            'fee_installment_id' => $request->fee_installment_id,
            'amount_paid' => $request->amount_paid,
            'payment_method' => $request->payment_method,
            'transaction_reference' => $request->transaction_reference,
            'receipt_date' => $request->receipt_date,
            'screenshot' => $screenshotPath,
            'status' => 'Pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment proof submitted successfully and is pending verification.',
            'payment_id' => $payment->id
        ], 201);
    }

    /**
     * Securely download verified payment receipt PDF.
     */
    public function downloadReceipt(Request $request, $id)
    {
        $admission = $request->user()->admission;
        if (!$admission) {
            return response()->json(['success' => false, 'message' => 'Profile not found.'], 404);
        }

        $payment = FeePayment::where('admission_id', $admission->id)
            ->where('id', $id)
            ->first();

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment record not found.'
            ], 404);
        }

        if ($payment->status !== 'Verified') {
            return response()->json([
                'success' => false,
                'message' => 'Only verified payments have receipts available for download.'
            ], 403);
        }

        return app(PDFController::class)->downloadReceipt($payment);
    }

    /**
     * Render the student photo securely using signed URL verification.
     */
    public function getStudentPhoto(string $path)
    {
        if (!Storage::disk('local')->exists($path)) {
            abort(404);
        }

        return Storage::disk('local')->response($path);
    }

    /**
     * Store or update a Google FCM token for the authenticated user.
     */
    public function storeFcmToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fcm_token' => 'required|string',
            'device_name' => 'nullable|string|max:255',
            'device_type' => 'nullable|string|in:android,ios,web',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $token = $request->input('fcm_token');
        $deviceName = $request->input('device_name');
        $deviceType = $request->input('device_type');

        // Check if this token is already registered to someone else or to this user
        $existing = FcmToken::where('fcm_token', $token)->first();

        if ($existing) {
            $existing->update([
                'user_id' => $user->id,
                'device_name' => $deviceName ?? $existing->device_name,
                'device_type' => $deviceType ?? $existing->device_type,
            ]);
            $fcmToken = $existing;
        } else {
            $fcmToken = FcmToken::create([
                'user_id' => $user->id,
                'fcm_token' => $token,
                'device_name' => $deviceName,
                'device_type' => $deviceType,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'FCM token stored successfully.',
            'data' => [
                'id' => $fcmToken->id,
                'device_name' => $fcmToken->device_name,
                'device_type' => $fcmToken->device_type,
            ]
        ]);
    }

    /**
     * Revoke / delete a Google FCM token.
     */
    public function revokeFcmToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fcm_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $token = $request->input('fcm_token');

        $deleted = FcmToken::where('user_id', $user->id)
            ->where('fcm_token', $token)
            ->delete();

        if ($deleted) {
            return response()->json([
                'success' => true,
                'message' => 'FCM token revoked successfully.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'FCM token not found or not associated with this user.'
        ], 404);
    }

    /**
     * Dispatch a test push notification to all stored FCM tokens of the user.
     */
    public function testSendNotification(Request $request)
    {
        $user = $request->user();
        
        $tokens = FcmToken::where('user_id', $user->id)->get();
        
        if ($tokens->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No registered FCM tokens found for this user.'
            ], 404);
        }

        $firebaseService = app(FirebaseService::class);
        $results = [];

        foreach ($tokens as $token) {
            $sent = $firebaseService->sendMessage(
                $token->fcm_token,
                'Test Notification',
                'This is a test notification from the Student Management System!',
                [
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    'sender' => 'StudentManagementSystemAPI'
                ]
            );
            
            $results[] = [
                'id' => $token->id,
                'device_name' => $token->device_name,
                'sent' => $sent
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Test notification dispatch processed.',
            'results' => $results
        ]);
    }

    /**
     * Get paginated notifications for the authenticated student.
     */
    public function getNotifications(Request $request)
    {
        $user = $request->user();
        $userIds = [$user->id];

        $admission = $user->admission;
        if ($admission) {
            $query = \App\Models\Admission::query();
            if ($admission->mobile) {
                $query->orWhere('mobile', $admission->mobile);
            }
            if ($admission->email) {
                $query->orWhere('email', $admission->email);
            }
            $linkedUserIds = $query->pluck('user_id')->filter()->toArray();
            $userIds = array_unique(array_merge($userIds, $linkedUserIds));
        }

        $notifications = Notification::whereIn('user_id', $userIds)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'notifications' => $notifications
        ]);
    }

    /**
     * Mark a specific notification as read.
     */
    public function markNotificationAsRead(Request $request, $id)
    {
        $user = $request->user();
        $userIds = [$user->id];

        $admission = $user->admission;
        if ($admission) {
            $query = \App\Models\Admission::query();
            if ($admission->mobile) {
                $query->orWhere('mobile', $admission->mobile);
            }
            if ($admission->email) {
                $query->orWhere('email', $admission->email);
            }
            $linkedUserIds = $query->pluck('user_id')->filter()->toArray();
            $userIds = array_unique(array_merge($userIds, $linkedUserIds));
        }

        $notification = Notification::whereIn('user_id', $userIds)
            ->where('id', $id)
            ->first();

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found.'
            ], 404);
        }

        $notification->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read.'
        ]);
    }

    /**
     * Mark all notifications as read for the authenticated student.
     */
    public function markAllNotificationsAsRead(Request $request)
    {
        $user = $request->user();
        $userIds = [$user->id];

        $admission = $user->admission;
        if ($admission) {
            $query = \App\Models\Admission::query();
            if ($admission->mobile) {
                $query->orWhere('mobile', $admission->mobile);
            }
            if ($admission->email) {
                $query->orWhere('email', $admission->email);
            }
            $linkedUserIds = $query->pluck('user_id')->filter()->toArray();
            $userIds = array_unique(array_merge($userIds, $linkedUserIds));
        }

        Notification::whereIn('user_id', $userIds)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read.'
        ]);
    }

    /**
     * Change password for authenticated student.
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password does not match.'
            ], 400);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully.'
        ]);
    }

    /**
     * Reset password for guest student using email and mobile verification.
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'mobile' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Verify that the admission record matches the email and mobile combination
        $admission = \App\Models\Admission::where('email', $request->email)
            ->where('mobile', $request->mobile)
            ->first();

        if (!$admission || !$admission->user) {
            return response()->json([
                'success' => false,
                'message' => 'No matching student record found for the provided email and mobile number.'
            ], 404);
        }

        $user = $admission->user;
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password has been reset successfully.'
        ]);
    }
}
