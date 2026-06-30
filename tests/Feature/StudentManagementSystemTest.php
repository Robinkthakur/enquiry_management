<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Admin;
use App\Models\Course;
use App\Models\Enquiry;
use App\Models\Admission;
use App\Models\AdmissionCourse;
use App\Models\FeeInstallment;
use App\Models\FeeHold;
use App\Models\FeePayment;
use App\Models\Certificate;
use App\Notifications\DueFeeReminderNotification;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class StudentManagementSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed the database
        $this->seed(DatabaseSeeder::class);
    }

    /**
     * Test system roles and permissions configuration.
     */
    public function test_user_roles_and_permissions_seeding()
    {
        $this->assertTrue(Role::where('name', 'Super Admin')->exists());
        $this->assertTrue(Role::where('name', 'Admin')->exists());
        $this->assertTrue(Role::where('name', 'Counselor')->exists());
        $this->assertTrue(Role::where('name', 'Accountant')->exists());
        $this->assertTrue(Role::where('name', 'Instructor')->exists());

        $counselor = Admin::role('Counselor')->first();
        $this->assertNotNull($counselor);
        $this->assertTrue($counselor->hasPermissionTo('enquiries.create'));
        $this->assertFalse($counselor->hasPermissionTo('roles.manage'));

        $accountant = Admin::role('Accountant')->first();
        $this->assertNotNull($accountant);
        $this->assertTrue($accountant->hasPermissionTo('fees.manage'));
        $this->assertFalse($accountant->hasPermissionTo('courses.create'));
    }

    /**
     * Test enquiry creation and subsequent conversion to admission with fee installments.
     */
    public function test_enquiry_to_admission_conversion()
    {
        // 1. Create a course and batch
        $course = Course::create([
            'course_code' => 'TEST1',
            'course_name' => 'Test Course 1',
            'description' => 'Test description',
            'duration_months' => 3,
            'total_fee' => 1000.00,
            'registration_fee' => 100.00,
            'certificate_fee' => 20.00,
            'tax_percentage' => 10.00,
            'status' => 'active',
        ]);

        $instructor = Admin::role('Instructor')->first();

        // 2. Create an enquiry
        $enquiry = Enquiry::create([
            'enquiry_no' => 'ENQ-TEST-01',
            'name' => 'Alice Test',
            'father_name' => 'Bob Test',
            'mobile' => '1234567890',
            'email' => 'alice@test.com',
            'gender' => 'Female',
            'date_of_birth' => '2000-01-01',
            'qualification' => 'Undergraduate',
            'occupation' => 'Student',
            'address' => 'Test Address',
            'enquiry_source' => 'Google Search',
            'status' => 'New',
            'taken_by' => Admin::role('Counselor')->first()->id,
        ]);
        $enquiry->interestedCourses()->attach($course->id);

        $this->assertEquals('New', $enquiry->status);

        // 3. Trigger conversion logic (mimic the action performed in EnquiryResource)
        $admissionDate = now()->toDateString();

        $admission = Admission::create([
            'enquiry_id' => $enquiry->id,
            'student_name' => $enquiry->name,
            'father_name' => $enquiry->father_name,
            'mobile' => $enquiry->mobile,
            'email' => $enquiry->email,
            'address' => $enquiry->address,
            'admission_date' => $admissionDate,
            'status' => 'Active',
        ]);

        $enrollment = AdmissionCourse::create([
            'admission_id' => $admission->id,
            'course_id' => $course->id,
            'time_slot' => '10:00 AM - 12:00 PM',
            'instructor_id' => $instructor->id,
            'total_fee' => $course->total_fee,
            'discount_amount' => 0.00,
            'final_fee' => $course->total_fee,
            'registration_fee' => 0.00,
            'status' => 'Active',
        ]);

        $enquiry->update(['status' => 'Admitted']);

        // Assertions
        $this->assertEquals('Admitted', $enquiry->fresh()->status);
        $this->assertEquals('Active', $admission->fresh()->status);
        $this->assertEquals(1, $admission->installments()->count());

        // Single installment details
        $inst1 = $admission->installments()->first();
        $this->assertEquals($course->total_fee, $inst1->amount);
        $this->assertEquals($admissionDate, $inst1->due_date->toDateString());
    }

    /**
     * Test that instructors are scoped to their assigned students.
     */
    public function test_instructor_student_scoping()
    {
        $instructorAlpha = Admin::where('email', 'instructor@sms.com')->first();
        $instructorBeta = Admin::where('email', 'instructor2@sms.com')->first();

        $course = Course::first();

        // Create admissions for each instructor
        $admissionAlpha = Admission::create([
            'admission_no' => 'ADM-A-01',
            'roll_no' => 'ROLL-A-01',
            'student_name' => 'Alpha Student',
            'mobile' => '1234567890',
            'admission_date' => now()->toDateString(),
            'status' => 'Active',
        ]);

        AdmissionCourse::create([
            'admission_id' => $admissionAlpha->id,
            'course_id' => $course->id,
            'time_slot' => '09:00 AM - 11:00 AM',
            'instructor_id' => $instructorAlpha->id,
            'total_fee' => 1000.00,
            'discount_amount' => 0.00,
            'final_fee' => 1000.00,
            'registration_fee' => 100.00,
            'status' => 'Active',
        ]);

        $admissionBeta = Admission::create([
            'admission_no' => 'ADM-B-01',
            'roll_no' => 'ROLL-B-01',
            'student_name' => 'Beta Student',
            'mobile' => '0987654321',
            'admission_date' => now()->toDateString(),
            'status' => 'Active',
        ]);

        AdmissionCourse::create([
            'admission_id' => $admissionBeta->id,
            'course_id' => $course->id,
            'time_slot' => '11:00 AM - 01:00 PM',
            'instructor_id' => $instructorBeta->id,
            'total_fee' => 1000.00,
            'discount_amount' => 0.00,
            'final_fee' => 1000.00,
            'registration_fee' => 100.00,
            'status' => 'Active',
        ]);

        // Simulating the Eloquent query restriction that would run in AttendanceResource/Policies
        $this->actingAs($instructorAlpha, 'admin');
        $alphaQuery = Admission::whereHas('enrollments', fn($q) => $q->where('instructor_id', auth()->id()))->get();
        $this->assertTrue($alphaQuery->contains($admissionAlpha));
        $this->assertFalse($alphaQuery->contains($admissionBeta));

        $this->actingAs($instructorBeta, 'admin');
        $betaQuery = Admission::whereHas('enrollments', fn($q) => $q->where('instructor_id', auth()->id()))->get();
        $this->assertTrue($betaQuery->contains($admissionBeta));
        $this->assertFalse($betaQuery->contains($admissionAlpha));
    }

    /**
     * Test fee reminder holds and the console command behavior.
     */
    public function test_fee_reminder_holds_and_reminders()
    {
        Notification::fake();

        $admission = Admission::first();
        $this->assertNotNull($admission);

        // Make sure there is an installment due today
        $installment = $admission->installments()->first();
        $installment->update([
            'due_date' => now()->toDateString(),
            'status' => 'Pending',
            'due_amount' => $installment->amount,
            'paid_amount' => 0,
        ]);

        // Step A: Active Hold present - no notification should be sent
        FeeHold::create([
            'admission_id' => $admission->id,
            'hold_from' => now()->subDays(1)->toDateString(),
            'hold_to' => now()->addDays(2)->toDateString(),
            'reason' => 'Medical hold',
            'approved_by' => Admin::role('Accountant')->first()->id,
        ]);

        $this->assertTrue($admission->fresh()->hasActiveHold());

        Artisan::call('app:send-fee-reminders');

        Notification::assertNothingSent();

        // Step B: Active Hold expires/removed - notification should be sent
        $admission->holds()->delete();
        $this->assertFalse($admission->fresh()->hasActiveHold());

        Artisan::call('app:send-fee-reminders');

        Notification::assertSentTo(
            $admission,
            DueFeeReminderNotification::class,
            function ($notification, $channels) {
                return in_array('mail', $channels);
            }
        );
    }

    /**
     * Test certificate generation and verification.
     */
    public function test_certificate_generation_and_verification()
    {
        $admission = Admission::first();
        $course = Course::first();

        // Generate Certificate
        $certificate = Certificate::create([
            'admission_id' => $admission->id,
            'course_id' => $course->id,
            'issue_date' => now()->toDateString(),
            'completion_date' => now()->subDays(5)->toDateString(),
        ]);

        $this->assertNotEmpty($certificate->certificate_no);
        $this->assertNotEmpty($certificate->verification_token);
        $this->assertEquals(32, strlen($certificate->verification_token));

        // Test public verification route
        $response = $this->get(route('certificates.verify', ['token' => $certificate->verification_token]));
        $response->assertStatus(200);
        $response->assertSee($certificate->certificate_no);
        $response->assertSee($admission->student_name);

        // Test invalid token
        $responseInvalid = $this->get(route('certificates.verify', ['token' => 'invalidtoken123']));
        $responseInvalid->assertStatus(200);
        $responseInvalid->assertSee('Invalid Certificate');
    }

    /**
     * Test reports page exports.
     */
    public function test_reports_page_exports()
    {
        $admin = Admin::role('Super Admin')->first();
        $this->actingAs($admin, 'admin');
        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('admin'));

        \Livewire\Livewire::test(\App\Filament\Admin\Pages\Reports::class)
            ->call('exportPdf')
            ->assertFileDownloaded();

        \Livewire\Livewire::test(\App\Filament\Admin\Pages\Reports::class)
            ->call('exportCsv')
            ->assertFileDownloaded();
    }

    /**
     * Test fee installments filter tabs.
     */
    public function test_fee_installments_filter_tabs()
    {
        $admin = Admin::role('Super Admin')->first();
        $this->actingAs($admin, 'admin');
        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('admin'));

        $listPage = new \App\Filament\Admin\Resources\FeeInstallmentResource\Pages\ListFeeInstallments();
        $tabs = $listPage->getTabs();

        $this->assertArrayHasKey('all', $tabs);
        $this->assertArrayHasKey('this_month', $tabs);
        $this->assertArrayHasKey('overdue', $tabs);
        $this->assertArrayHasKey('unpaid', $tabs);
        $this->assertArrayHasKey('paid', $tabs);

        $this->assertInstanceOf(\Filament\Schemas\Components\Tabs\Tab::class, $tabs['all']);
        $this->assertInstanceOf(\Filament\Schemas\Components\Tabs\Tab::class, $tabs['this_month']);
        $this->assertInstanceOf(\Filament\Schemas\Components\Tabs\Tab::class, $tabs['overdue']);
        $this->assertInstanceOf(\Filament\Schemas\Components\Tabs\Tab::class, $tabs['unpaid']);
        $this->assertInstanceOf(\Filament\Schemas\Components\Tabs\Tab::class, $tabs['paid']);
    }

    /**
     * Test company settings configuration, seeding, and updates.
     */
    public function test_company_settings_functionality()
    {
        // 1. Verify seeder created the default setting
        $setting = \App\Models\CompanySetting::first();
        $this->assertNotNull($setting);
        $this->assertEquals('EDU INSTITUTE', $setting->company_name);

        // 2. Access Settings Page as Admin
        $admin = Admin::role('Super Admin')->first();
        $this->actingAs($admin, 'admin');
        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('admin'));

        \Livewire\Livewire::test(\App\Filament\Admin\Pages\CompanySettings::class)
            ->fillForm([
                'company_name' => 'NEW COMPANY NAME',
                'support_email' => 'contact@newcompany.com',
            ])
            ->call('save');

        // 3. Verify changes stored in database
        $this->assertEquals('NEW COMPANY NAME', \App\Models\CompanySetting::first()->company_name);
        $this->assertEquals('contact@newcompany.com', \App\Models\CompanySetting::first()->support_email);
    }

    /**
     * Test multiple courses update functionality on the Edit page.
     */
    public function test_multiple_courses_update_functionality()
    {
        $admin = Admin::role('Super Admin')->first();
        $this->actingAs($admin, 'admin');
        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('admin'));

        $admission = Admission::first();
        $this->assertNotNull($admission);

        $course2 = Course::where('id', '!=', $admission->course_id)->first();
        $this->assertNotNull($course2);

        $enrollment1 = $admission->enrollments()->first();
        $this->assertNotNull($enrollment1);

        $instructor = Admin::role('Instructor')->first();

        // Access the Edit page, add another course enrollment, change name, and save
        \Livewire\Livewire::test(\App\Filament\Admin\Resources\AdmissionResource\Pages\EditAdmission::class, [
            'record' => $admission->id,
        ])
            ->fillForm([
                'student_name' => 'John Edited',
                'mobile' => $admission->mobile,
                'email' => $admission->email,
                'enrollments' => [
                    [
                        'id' => $enrollment1->id,
                        'course_id' => $enrollment1->course_id,
                        'start_time' => '10:00 AM',
                        'end_time' => '12:00 PM',
                        'time_slot' => '10:00 AM - 12:00 PM',
                        'instructor_id' => $enrollment1->instructor_id,
                        'total_fee' => 1000,
                        'registration_fee' => 0,
                        'discount_amount' => 0,
                        'final_fee' => 1000,
                        'status' => 'Active',
                    ],
                    [
                        'course_id' => $course2->id,
                        'start_time' => '02:00 PM',
                        'end_time' => '04:00 PM',
                        'time_slot' => '02:00 PM - 04:00 PM',
                        'instructor_id' => $instructor->id,
                        'total_fee' => 1200,
                        'registration_fee' => 0,
                        'discount_amount' => 0,
                        'final_fee' => 1200,
                        'status' => 'Active',
                    ]
                ]
            ])
            ->call('save');

        // Verify that there is now 1 admission with the edited name and mobile
        $admissions = Admission::where('mobile', $admission->mobile)->get();
        $this->assertCount(1, $admissions);
        $this->assertEquals('John Edited', $admissions[0]->student_name);
        
        // Verify that the second course enrollment was created
        $student = $admissions[0];
        $this->assertCount(2, $student->enrollments);
        
        $newEnrollment = $student->enrollments()->where('course_id', $course2->id)->first();
        $this->assertNotNull($newEnrollment);
        $this->assertEquals('02:00 PM - 04:00 PM', $newEnrollment->time_slot);
        $this->assertEquals(1200, $newEnrollment->final_fee);
    }

    /**
     * Test admin user access and deletion restrictions.
     */
    public function test_admin_user_management_and_deletion_restrictions()
    {
        $superAdmin = Admin::role('Super Admin')->first();
        $admin = Admin::role('Admin')->first();

        // 1. Admin can viewAny users
        $this->actingAs($admin, 'admin');
        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('admin'));
        $this->assertTrue($admin->can('viewAny', Admin::class));

        // 2. Admin can create users
        $this->assertTrue($admin->can('create', Admin::class));

        // 3. Admin can update users
        $newUser = Admin::factory()->create();
        $this->assertTrue($admin->can('update', $newUser));

        // 4. Admin cannot delete a Super Admin
        $this->assertFalse($admin->can('delete', $superAdmin));

        // 5. Super Admin checks return true at policy/gate level due to Gate::before bypass
        $this->assertTrue($superAdmin->can('delete', $superAdmin));

        // Let's create a second Super Admin user to test policy check on another Super Admin
        $secondSuperAdmin = Admin::factory()->create();
        $secondSuperAdmin->assignRole('Super Admin');
        $this->assertTrue($superAdmin->can('delete', $secondSuperAdmin));

        // 6. Admin can delete a normal user
        $normalUser = Admin::factory()->create();
        $this->assertTrue($admin->can('delete', $normalUser));
    }

    /**
     * Test that non-Super Admins cannot access roles, permissions, audit trails, and company settings.
     */
    public function test_restricted_modules_access()
    {
        $superAdmin = Admin::role('Super Admin')->first();
        $admin = Admin::role('Admin')->first();

        // 1. Roles & Permissions policies
        $this->actingAs($admin, 'admin');
        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('admin'));
        $this->assertFalse($admin->can('viewAny', Role::class));
        $this->assertFalse($admin->can('viewAny', Permission::class));

        $this->actingAs($superAdmin, 'admin');
        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('admin'));
        $this->assertTrue($superAdmin->can('viewAny', Role::class));
        $this->assertTrue($superAdmin->can('viewAny', Permission::class));

        // 2. Audit Trails page (ActivityLogResource)
        $this->actingAs($admin, 'admin');
        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('admin'));
        $this->assertFalse(\App\Filament\Admin\Resources\ActivityLogResource::canViewAny());

        $this->actingAs($superAdmin, 'admin');
        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('admin'));
        $this->assertTrue(\App\Filament\Admin\Resources\ActivityLogResource::canViewAny());

        // 3. Company Settings page
        $this->actingAs($admin, 'admin');
        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('admin'));
        $this->assertFalse(\App\Filament\Admin\Pages\CompanySettings::canAccess());

        $this->actingAs($superAdmin, 'admin');
        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('admin'));
        $this->assertTrue(\App\Filament\Admin\Pages\CompanySettings::canAccess());
    }

    /**
     * Test user deletion restrictions at Filament UI action and bulk action levels.
     */
    public function test_user_deletion_restrictions_via_filament()
    {
        $superAdmin = Admin::role('Super Admin')->first();
        $admin = Admin::role('Admin')->first();
        $normalUser = Admin::factory()->create();

        // 1. Acting as Admin
        $this->actingAs($admin, 'admin');
        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('admin'));

        // Admin should not see 'delete' action for a Super Admin row, but should see it for normal users
        \Livewire\Livewire::test(\App\Filament\Admin\Resources\UserResource\Pages\ManageUsers::class)
            ->assertTableActionHidden('delete', $superAdmin)
            ->assertTableActionVisible('delete', $normalUser);

        // 2. Acting as Super Admin
        $this->actingAs($superAdmin, 'admin');
        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('admin'));

        // Super Admin should see the action (due to Gate::before bypass), but calling it should halt and NOT delete
        \Livewire\Livewire::test(\App\Filament\Admin\Resources\UserResource\Pages\ManageUsers::class)
            ->callTableAction('delete', $superAdmin);

        $this->assertDatabaseHas('admins', ['id' => $superAdmin->id]);

        // Same for another Super Admin
        $secondSuperAdmin = Admin::factory()->create();
        $secondSuperAdmin->assignRole('Super Admin');

        \Livewire\Livewire::test(\App\Filament\Admin\Resources\UserResource\Pages\ManageUsers::class)
            ->callTableAction('delete', $secondSuperAdmin);

        $this->assertDatabaseHas('admins', ['id' => $secondSuperAdmin->id]);

        // Verify that a normal user CAN be deleted by Admin
        $this->actingAs($admin, 'admin');
        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('admin'));
        \Livewire\Livewire::test(\App\Filament\Admin\Resources\UserResource\Pages\ManageUsers::class)
            ->callTableAction('delete', $normalUser);

        $this->assertDatabaseMissing('admins', ['id' => $normalUser->id]);
    }

    /**
     * Test secure retrieval of private student photos.
     */
    public function test_secure_private_student_photo_retrieval()
    {
        \Illuminate\Support\Facades\Storage::fake('local');
        \Illuminate\Support\Facades\Storage::disk('local')->put('student_photos/avatar.jpg', 'imagecontent');

        // Unauthenticated user should be redirected
        $response = $this->get('/admin/student-photos/student_photos/avatar.jpg');
        $response->assertStatus(302);

        // Authenticated user should be able to view it
        $user = Admin::first();
        $response = $this->actingAs($user, 'admin')->get('/admin/student-photos/student_photos/avatar.jpg');
        $response->assertStatus(200);
        $this->assertEquals('imagecontent', $response->streamedContent());
    }

    /**
     * Test panel access control for Student and Admin roles.
     */
    public function test_student_and_admin_panel_access_controls()
    {
        $admin = Admin::role('Super Admin')->first();
        
        // Find or create student user
        $admission = Admission::first();
        $studentUser = $admission->user;
        $this->assertNotNull($studentUser);
        $this->assertTrue($studentUser->hasRole('Student'));

        // 1. Admin should be allowed in admin panel but not student panel
        $responseAdminToAdmin = $this->actingAs($admin, 'admin')->get('/admin');
        if ($responseAdminToAdmin->isRedirect()) {
            $responseAdminToAdmin = $this->followRedirects($responseAdminToAdmin);
        }
        $responseAdminToAdmin->assertStatus(200);

        $responseAdminToStudent = $this->actingAs($admin, 'admin')->get('/student/dashboard');
        $this->assertTrue(in_array($responseAdminToStudent->getStatusCode(), [302, 403]));

        // 2. Student should be allowed in student panel but not admin panel
        $responseStudentToStudent = $this->actingAs($studentUser)->get('/student/dashboard');
        if ($responseStudentToStudent->isRedirect()) {
            $responseStudentToStudent = $this->followRedirects($responseStudentToStudent);
        }
        $responseStudentToStudent->assertStatus(200);

        $responseStudentToAdmin = $this->actingAs($studentUser)->get('/admin');
        $this->assertTrue(in_array($responseStudentToAdmin->getStatusCode(), [302, 403]));
    }

    /**
     * Test student profile update (email and image/photo sync).
     */
    public function test_student_profile_update()
    {
        $admission = Admission::first();
        $studentUser = $admission->user;

        $this->actingAs($studentUser);
        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('student'));

        \Illuminate\Support\Facades\Storage::fake('local');
        $photo = \Illuminate\Http\UploadedFile::fake()->image('new_photo.jpg');

        \Livewire\Livewire::test(\App\Filament\Student\Pages\ProfileSettings::class)
            ->fillForm([
                'email' => 'updated.student@example.com',
                'student_photo' => $photo,
            ])
            ->call('save');

        // Check if both user and admission emails are updated and student_photo is updated
        $this->assertEquals('updated.student@example.com', $studentUser->fresh()->email);
        $this->assertEquals('updated.student@example.com', $admission->fresh()->email);
        $this->assertNotNull($admission->fresh()->student_photo);
    }

    /**
     * Test leave application creation by student and approval by admin.
     */
    public function test_leave_application_student_flow_and_admin_approval()
    {
        $admission = Admission::first();
        $studentUser = $admission->user;
        $admin = Admin::role('Super Admin')->first();

        // 1. Student creates leave application
        $this->actingAs($studentUser);
        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('student'));

        \Livewire\Livewire::test(\App\Filament\Student\Resources\LeaveApplicationResource\Pages\CreateLeaveApplication::class)
            ->fillForm([
                'start_date' => now()->addDays(2)->toDateString(),
                'end_date' => now()->addDays(5)->toDateString(),
                'reason' => 'Need leave for personal work.',
            ])
            ->call('create');

        $this->assertDatabaseHas('leave_applications', [
            'admission_id' => $admission->id,
            'reason' => 'Need leave for personal work.',
            'status' => 'Pending',
        ]);

        $leave = \App\Models\LeaveApplication::where('admission_id', $admission->id)->first();
        $this->assertNotNull($leave);

        // 2. Admin approves the leave application
        $this->actingAs($admin, 'admin');
        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('admin'));
        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('admin'));

        \Livewire\Livewire::test(\App\Filament\Admin\Resources\LeaveApplicationResource\Pages\ManageLeaveApplications::class)
            ->callTableAction('approve', $leave, [
                'admin_remarks' => 'Approved, have a good time.',
            ]);

        $this->assertEquals('Approved', $leave->fresh()->status);
        $this->assertEquals('Approved, have a good time.', $leave->fresh()->admin_remarks);
    }

    /**
     * Test student self course enrollment.
     */
    public function test_student_self_course_enrollment()
    {
        $admission = Admission::first();
        $studentUser = $admission->user;
        
        // Find a course student is NOT enrolled in yet
        $enrolledCourseIds = $admission->enrollments()->pluck('course_id')->toArray();
        $course = Course::whereNotIn('id', $enrolledCourseIds)->first();
        $this->assertNotNull($course);

        $this->actingAs($studentUser);
        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('student'));

        \Livewire\Livewire::test(\App\Filament\Student\Resources\EnrollmentResource\Pages\CreateEnrollment::class)
            ->fillForm([
                'course_id' => $course->id,
                'start_time' => '04:00 PM',
                'end_time' => '06:00 PM',
                'time_slot' => '04:00 PM - 06:00 PM',
            ])
            ->call('create');

        // Check enrollment exists and fees matched
        $this->assertDatabaseHas('admission_courses', [
            'admission_id' => $admission->id,
            'course_id' => $course->id,
            'total_fee' => $course->total_fee,
            'final_fee' => $course->total_fee,
            'registration_fee' => $course->registration_fee,
            'status' => 'Active',
        ]);
    }

    /**
     * Test student payment QR upload, admin verification, and receipt downloading.
     */
    public function test_student_qr_payment_and_admin_verification_flow()
    {
        $admission = Admission::first();
        $studentUser = $admission->user;
        $admin = Admin::role('Super Admin')->first();
        
        $installment = FeeInstallment::create([
            'admission_id' => $admission->id,
            'admission_course_id' => $admission->enrollments()->first()->id,
            'installment_no' => 2,
            'due_date' => now()->addMonth(),
            'amount' => 500.00,
            'paid_amount' => 0.00,
            'due_amount' => 500.00,
            'status' => 'Pending',
        ]);

        // 1. Student uploads screenshot proof via QR pay action
        $this->actingAs($studentUser);
        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('student'));

        \Illuminate\Support\Facades\Storage::fake('local');
        $screenshot = \Illuminate\Http\UploadedFile::fake()->image('proof.jpg');

        \Livewire\Livewire::test(\App\Filament\Student\Resources\FeeInstallmentResource\Pages\ListFeeInstallments::class)
            ->callTableAction('pay_qr', $installment, [
                'amount_paid' => 500.00,
                'payment_method' => 'UPI/QR Code',
                'transaction_reference' => 'TXN123456789',
                'receipt_date' => now()->toDateString(),
                'screenshot' => $screenshot,
            ]);

        // Verify payment record is created as Pending
        $this->assertDatabaseHas('fee_payments', [
            'admission_id' => $admission->id,
            'fee_installment_id' => $installment->id,
            'amount_paid' => 500.00,
            'status' => 'Pending',
            'transaction_reference' => 'TXN123456789',
        ]);

        $payment = FeePayment::where('transaction_reference', 'TXN123456789')->first();
        $this->assertNotNull($payment->screenshot);

        // Verify installment due_amount has NOT changed (still 500.00 because payment is pending)
        $this->assertEquals(500.00, $installment->fresh()->due_amount);

        // 2. Admin verifies the payment
        $this->actingAs($admin, 'admin');
        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('admin'));
        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('admin'));

        \Livewire\Livewire::test(\App\Filament\Admin\Resources\FeePaymentResource\Pages\ListFeePayments::class)
            ->callTableAction('verify_payment', $payment);

        // Verify status changed to Verified
        $this->assertEquals('Verified', $payment->fresh()->status);

        // Verify installment is now Paid
        $this->assertEquals(0.00, $installment->fresh()->due_amount);
        $this->assertEquals('Paid', $installment->fresh()->status);
        
        // 3. Verify student can access the list of receipts
        $this->actingAs($studentUser);
        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('student'));

        $receiptsList = \Livewire\Livewire::test(\App\Filament\Student\Resources\ReceiptResource\Pages\ListReceipts::class);
        $receiptsList->assertStatus(200);
    }

    /**
     * Test student attendance logs list page and stats widget.
     */
    public function test_student_attendance_logs_and_stats()
    {
        $admission = Admission::first();
        $studentUser = $admission->user;

        $this->actingAs($studentUser);
        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('student'));

        \Livewire\Livewire::test(\App\Filament\Student\Resources\AttendanceResource\Pages\ListAttendances::class)
            ->assertStatus(200);
    }

    /**
     * Test student enrollment view details page.
     */
    public function test_student_enrollment_view_details()
    {
        $admission = Admission::first();
        $studentUser = $admission->user;
        $enrollment = $admission->enrollments()->first();

        $this->actingAs($studentUser);
        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('student'));

        \Livewire\Livewire::test(\App\Filament\Student\Resources\EnrollmentResource\Pages\ViewEnrollment::class, [
            'record' => $enrollment->id,
        ])
        ->assertStatus(200);
    }

    /**
     * Test student cannot apply for leave if another leave application is Pending or Approved.
     */
    public function test_student_leave_application_limit_rule()
    {
        $admission = Admission::first();
        $studentUser = $admission->user;

        $this->actingAs($studentUser);
        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('student'));

        // 1. Create first leave application (Pending)
        \Livewire\Livewire::test(\App\Filament\Student\Resources\LeaveApplicationResource\Pages\CreateLeaveApplication::class)
            ->fillForm([
                'start_date' => now()->addDays(2)->toDateString(),
                'end_date' => now()->addDays(5)->toDateString(),
                'reason' => 'First leave application.',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('leave_applications', [
            'admission_id' => $admission->id,
            'reason' => 'First leave application.',
            'status' => 'Pending',
        ]);

        // 2. Try to create second leave application, should fail validation
        \Livewire\Livewire::test(\App\Filament\Student\Resources\LeaveApplicationResource\Pages\CreateLeaveApplication::class)
            ->fillForm([
                'start_date' => now()->addDays(6)->toDateString(),
                'end_date' => now()->addDays(8)->toDateString(),
                'reason' => 'Second leave application.',
            ])
            ->call('create')
            ->assertHasFormErrors(['start_date']);
    }

    /**
     * Test mark attendance custom page loading, list population, and saving.
     */
    public function test_mark_attendance_custom_page_loading_and_saving()
    {
        $admin = Admin::role('Super Admin')->first();
        $this->actingAs($admin, 'admin');
        \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('admin'));

        // Access/Test Mark Attendance Livewire component
        $component = \Livewire\Livewire::test(\App\Filament\Admin\Pages\MarkAttendance::class);
        $component->assertStatus(200);

        // Verify that the list of students is loaded automatically
        $students = $component->get('students');
        $this->assertNotEmpty($students);

        // Verify search functionality
        $firstStudentName = $students->first()->student_name;
        $component->set('search', $firstStudentName);
        $this->assertNotEmpty($component->get('students'));

        $component->set('search', 'NonExistentStudentQueryXYZ');
        $this->assertEmpty($component->get('students'));

        // Reset search
        $component->set('search', '');

        // Mark a student status and save
        $studentId = $students->first()->id;
        $component->call('setStatus', $studentId, 'Absent');
        $component->call('save');

        // Assert that database has the attendance marked as Absent
        $this->assertDatabaseHas('attendances', [
            'admission_course_id' => $studentId,
            'status' => 'Absent',
        ]);
    }

    /**
     * Test Admission user account creation, linkage, and sync logic.
     */
    public function test_admission_user_linkage_and_update_behavior()
    {
        // 1. Create a user beforehand
        $existingEmail = 'existingstudent@example.com';
        $existingUser = User::create([
            'name' => 'Existing User',
            'email' => $existingEmail,
            'password' => \Illuminate\Support\Facades\Hash::make('secret123'),
        ]);

        // 2. Create admission with existing email -> should link to existing user
        $admission = Admission::create([
            'student_name' => 'Linked Student',
            'email' => $existingEmail,
            'mobile' => '9876543210',
            'admission_date' => now(),
            'status' => 'Active',
        ]);

        $this->assertEquals($existingUser->id, $admission->user_id);

        // 3. Create admission without email -> should not create a user
        $admissionNoEmail = Admission::create([
            'student_name' => 'No Email Student',
            'email' => null,
            'mobile' => '1234567890',
            'admission_date' => now(),
            'status' => 'Active',
        ]);

        $this->assertNull($admissionNoEmail->user_id);

        // 4. Edit admission -> should synchronize email and name to the linked user
        $admission->update([
            'student_name' => 'Linked Student Edited',
            'email' => 'updatedstudentemail@example.com',
        ]);

        $this->assertEquals('Linked Student Edited', $existingUser->fresh()->name);
        $this->assertEquals('updatedstudentemail@example.com', $existingUser->fresh()->email);
    }
}
