<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Course;
use App\Models\Admission;
use App\Models\AdmissionCourse;
use App\Models\FeeInstallment;
use App\Models\FeePayment;
use App\Models\LeaveApplication;
use App\Models\Attendance;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StudentMobileApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed the database
        $this->seed(DatabaseSeeder::class);
    }

    /**
     * Test student registration API.
     */
    public function test_api_student_registration()
    {
        // 1. Success Registration
        $response = $this->postJson('/api/register', [
            'name' => 'Alice Smith',
            'email' => 'alice.smith@example.com',
            'phone' => '1234567890',
            'password' => 'newpassword123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['success', 'message', 'token', 'user', 'student'])
            ->assertJsonPath('success', true)
            ->assertJsonPath('user.email', 'alice.smith@example.com')
            ->assertJsonPath('student.student_name', 'Alice Smith');

        $this->assertDatabaseHas('admissions', [
            'email' => 'alice.smith@example.com',
            'student_name' => 'Alice Smith',
            'mobile' => '1234567890',
        ]);

        $user = User::where('email', 'alice.smith@example.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('Student'));

        // 2. Failure: Duplicate email
        $responseDuplicate = $this->postJson('/api/register', [
            'name' => 'Alice Duplicate',
            'email' => 'alice.smith@example.com',
            'phone' => '0987654321',
            'password' => 'password123',
        ]);

        $responseDuplicate->assertStatus(422)
            ->assertJsonStructure(['success', 'errors'])
            ->assertJsonValidationErrors(['email']);

        // 3. Failure: Missing fields
        $responseMissing = $this->postJson('/api/register', [
            'name' => '',
            'email' => 'notanemail',
        ]);

        $responseMissing->assertStatus(422)
            ->assertJsonStructure(['success', 'errors'])
            ->assertJsonValidationErrors(['name', 'email', 'phone', 'password']);
    }

    /**
     * Test student login and token generation.
     */
    public function test_api_student_login()
    {
        $admission = Admission::first();
        $studentUser = $admission->user;

        // 1. Success Login
        $response = $this->postJson('/api/login', [
            'email' => $studentUser->email,
            'password' => $admission->mobile,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'token', 'user', 'student'])
            ->assertJsonPath('success', true);

        // Success Login via admission_no
        $responseAdm = $this->postJson('/api/login', [
            'email' => $admission->admission_no,
            'password' => $admission->mobile,
        ]);

        $responseAdm->assertStatus(200)
            ->assertJsonStructure(['success', 'token', 'user', 'student'])
            ->assertJsonPath('success', true);

        // 2. Failure: Invalid Credentials
        $responseFail = $this->postJson('/api/login', [
            'email' => $studentUser->email,
            'password' => 'wrongpassword',
        ]);
        $responseFail->assertStatus(401)
            ->assertJsonPath('success', false);

        // 3. Failure: Non-student login
        $adminUser = \App\Models\Admin::role('Super Admin')->first();
        $responseAdmin = $this->postJson('/api/login', [
            'email' => $adminUser->email,
            'password' => 'password',
        ]);
        $responseAdmin->assertStatus(403)
            ->assertJsonPath('success', false);
    }

    /**
     * Test authenticated profile API actions.
     */
    public function test_api_student_profile_management()
    {
        $admission = Admission::first();
        $studentUser = $admission->user;
        $token = $studentUser->createToken('test')->plainTextToken;

        // 1. Get Profile
        $responseGet = $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->getJson('/api/profile');

        $responseGet->assertStatus(200)
            ->assertJsonStructure(['success', 'profile'])
            ->assertJsonPath('profile.student_name', $admission->student_name);

        // 2. Update Profile
        Storage::fake('local');
        $photo = UploadedFile::fake()->image('avatar.jpg');

        $responseUpdate = $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->postJson('/api/profile/update', [
                'email' => 'updated.api.email@example.com',
                'student_photo' => $photo,
            ]);

        $responseUpdate->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertEquals('updated.api.email@example.com', $studentUser->fresh()->email);
        $this->assertEquals('updated.api.email@example.com', $admission->fresh()->email);
        $this->assertNotNull($admission->fresh()->student_photo);
    }

    /**
     * Test course details, enrollments, and self-enrollment.
     */
    public function test_api_student_enrollments()
    {
        $admission = Admission::first();
        $studentUser = $admission->user;
        $token = $studentUser->createToken('test')->plainTextToken;

        // 1. Fetch available courses
        $responseCourses = $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->getJson('/api/courses');
        $responseCourses->assertStatus(200)
            ->assertJsonStructure(['success', 'courses']);

        // 2. Fetch current enrollments
        $responseEnrollments = $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->getJson('/api/enrollments');
        $responseEnrollments->assertStatus(200)
            ->assertJsonStructure(['success', 'enrollments']);

        // 3. View enrollment details
        $enrollment = $admission->enrollments()->first();
        $responseDetails = $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->getJson("/api/enrollments/{$enrollment->id}");
        $responseDetails->assertStatus(200)
            ->assertJsonStructure(['success', 'details']);

        // 4. Enroll in a new course
        $enrolledIds = $admission->enrollments()->pluck('course_id')->toArray();
        $newCourse = Course::whereNotIn('id', $enrolledIds)->first();
        $this->assertNotNull($newCourse);

        $responseEnroll = $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->postJson('/api/enrollments', [
                'course_id' => $newCourse->id,
                'start_time' => '10:00 AM',
                'end_time' => '12:00 PM',
            ]);
        $responseEnroll->assertStatus(201)
            ->assertJsonPath('success', true);
    }

    /**
     * Test attendance list and stats.
     */
    public function test_api_student_attendance()
    {
        $admission = Admission::first();
        $studentUser = $admission->user;
        $token = $studentUser->createToken('test')->plainTextToken;

        // Add dummy attendance
        Attendance::create([
            'admission_id' => $admission->id,
            'admission_course_id' => $admission->enrollments()->first()->id,
            'attendance_date' => now()->toDateString(),
            'status' => 'Present',
        ]);

        $responseList = $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->getJson('/api/attendance');
        $responseList->assertStatus(200)
            ->assertJsonStructure(['success', 'logs']);

        $responseStats = $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->getJson('/api/attendance/stats');
        $responseStats->assertStatus(200)
            ->assertJsonPath('stats.total_conducted', 1)
            ->assertJsonPath('stats.attendance_rate', '100%');
    }

    /**
     * Test leave applications and validation constraints.
     */
    public function test_api_student_leaves()
    {
        $admission = Admission::first();
        $studentUser = $admission->user;
        $token = $studentUser->createToken('test')->plainTextToken;

        // 1. Apply for leave successfully
        $responseApply = $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->postJson('/api/leaves', [
                'start_date' => now()->addDays(2)->toDateString(),
                'end_date' => now()->addDays(5)->toDateString(),
                'reason' => 'Leave via REST API.',
            ]);
        $responseApply->assertStatus(201)
            ->assertJsonPath('success', true);

        // Verify leave exists in database
        $this->assertDatabaseHas('leave_applications', [
            'admission_id' => $admission->id,
            'reason' => 'Leave via REST API.',
            'status' => 'Pending',
        ]);

        // 2. Try to apply again while first is Pending (should fail with 422)
        $responseBlock = $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->postJson('/api/leaves', [
                'start_date' => now()->addDays(6)->toDateString(),
                'end_date' => now()->addDays(8)->toDateString(),
                'reason' => 'Second leave attempt.',
            ]);
        $responseBlock->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'You already have an active or pending leave application.');
    }

    /**
     * Test fee installments, pay QR payments, and PDF receipts.
     */
    public function test_api_student_installments_and_qr_payments()
    {
        $admission = Admission::first();
        $studentUser = $admission->user;
        $token = $studentUser->createToken('test')->plainTextToken;

        $installment = FeeInstallment::create([
            'admission_id' => $admission->id,
            'admission_course_id' => $admission->enrollments()->first()->id,
            'installment_no' => 2,
            'due_date' => now()->addMonth(),
            'amount' => 600.00,
            'paid_amount' => 0.00,
            'due_amount' => 600.00,
            'status' => 'Pending',
        ]);

        // 1. Get installments list
        $responseList = $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->getJson('/api/installments');
        $responseList->assertStatus(200)
            ->assertJsonStructure(['success', 'installments']);

        // 2. Pay installment via QR code
        Storage::fake('local');
        $screenshot = UploadedFile::fake()->image('payment_receipt.jpg');

        $responsePay = $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->postJson('/api/payments/pay-qr', [
                'fee_installment_id' => $installment->id,
                'amount_paid' => 600.00,
                'payment_method' => 'UPI/QR Code',
                'transaction_reference' => 'TXN-API-101',
                'receipt_date' => now()->toDateString(),
                'screenshot' => $screenshot,
            ]);

        $responsePay->assertStatus(201)
            ->assertJsonPath('success', true);

        // Verify database has pending payment
        $payment = FeePayment::where('transaction_reference', 'TXN-API-101')->first();
        $this->assertNotNull($payment);
        $this->assertEquals('Pending', $payment->status);

        // Due amount should still be 600.00 (since it is Pending verification)
        $this->assertEquals(600.00, $installment->fresh()->due_amount);

        // 3. Try downloading receipt for unverified payment (should fail with 403)
        $responseReceiptFail = $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->getJson("/api/payments/{$payment->id}/receipt");
        $responseReceiptFail->assertStatus(403);

        // 4. Verify payment (Admin side simulation)
        $payment->status = 'Verified';
        $payment->save();

        $this->assertEquals(0.00, $installment->fresh()->due_amount);
        $this->assertEquals('Paid', $installment->fresh()->status);

        // 5. Download receipt for verified payment (should succeed with PDF stream)
        $responseReceiptSuccess = $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->getJson("/api/payments/{$payment->id}/receipt");
        $responseReceiptSuccess->assertStatus(200)
            ->assertHeader('Content-Type', 'application/pdf');
    }

    /**
     * Test token revocation logout.
     */
    public function test_api_student_logout()
    {
        $admission = Admission::first();
        $studentUser = $admission->user;
        $token = $studentUser->createToken('test')->plainTextToken;

        $responseLogout = $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->postJson('/api/logout');

        $responseLogout->assertStatus(200)
            ->assertJsonPath('success', true);

        // Clear auth cache in testing container
        \Illuminate\Support\Facades\Auth::forgetGuards();

        // Accessing profile now should fail with 401 Unauthorized
        $responseGet = $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->getJson('/api/profile');
        $responseGet->assertStatus(401);
    }

    /**
     * Test student private photo signed URL security.
     */
    public function test_api_student_signed_photo_url()
    {
        Storage::fake('local');
        $admission = Admission::first();
        $studentUser = $admission->user;
        $token = $studentUser->createToken('test')->plainTextToken;

        // Upload a profile photo first
        $photo = UploadedFile::fake()->image('avatar.jpg');
        $responseUpdate = $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->postJson('/api/profile/update', [
                'email' => $admission->email,
                'student_photo' => $photo,
            ]);

        $responseUpdate->assertStatus(200);
        $photoUrl = $responseUpdate->json('profile.photo_url');
        $this->assertNotNull($photoUrl);

        // Verify the URL is signed and contains 'signature=' and 'expires='
        $this->assertStringContainsString('signature=', $photoUrl);
        $this->assertStringContainsString('expires=', $photoUrl);

        // 1. Success download using the signed URL
        $responseDownload = $this->get($photoUrl);
        $responseDownload->assertStatus(200);

        // 2. Failure: Modify signature (tampering check)
        $tamperedUrl = $photoUrl . 'tampered';
        $responseTampered = $this->get($tamperedUrl);
        $responseTampered->assertStatus(403);

        // 3. Failure: Missing signature
        $unsignedUrl = preg_replace('/(\?|&)(signature|expires)=[^&]+/', '', $photoUrl);
        $responseUnsigned = $this->get($unsignedUrl);
        $responseUnsigned->assertStatus(403);
    }

    /**
     * Test change and reset password APIs.
     */
    public function test_api_student_password_management()
    {
        $admission = Admission::first();
        $studentUser = $admission->user;
        $token = $studentUser->createToken('test')->plainTextToken;

        // 1. Change password (authenticated)
        $responseChange = $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->postJson('/api/password/change', [
                'current_password' => $admission->mobile,
                'password' => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ]);

        $responseChange->assertStatus(200)
            ->assertJsonPath('success', true);

        // Verify login works with new password
        $responseLogin = $this->postJson('/api/login', [
            'email' => $studentUser->email,
            'password' => 'newpassword123',
        ]);
        $responseLogin->assertStatus(200);

        // 2. Reset password (guest)
        $responseReset = $this->postJson('/api/password/reset', [
            'email' => $studentUser->email,
            'mobile' => $admission->mobile,
            'password' => 'resetpassword123',
            'password_confirmation' => 'resetpassword123',
        ]);

        $responseReset->assertStatus(200)
            ->assertJsonPath('success', true);

        // Verify login works with reset password
        $responseLoginReset = $this->postJson('/api/login', [
            'email' => $studentUser->email,
            'password' => 'resetpassword123',
        ]);
        $responseLoginReset->assertStatus(200);
    }
}
