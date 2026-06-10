<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Course;
use App\Models\Enquiry;
use App\Models\Admission;
use App\Models\FeeInstallment;
use App\Models\FeeHold;
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

        $counselor = User::role('Counselor')->first();
        $this->assertNotNull($counselor);
        $this->assertTrue($counselor->hasPermissionTo('enquiries.create'));
        $this->assertFalse($counselor->hasPermissionTo('roles.manage'));

        $accountant = User::role('Accountant')->first();
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

        $instructor = User::role('Instructor')->first();

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
            'taken_by' => User::role('Counselor')->first()->id,
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
            'course_id' => $course->id,
            'time_slot' => '10:00 AM - 12:00 PM',
            'instructor_id' => $instructor->id,
            'admission_date' => $admissionDate,
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
        $instructorAlpha = User::where('email', 'instructor@sms.com')->first();
        $instructorBeta = User::where('email', 'instructor2@sms.com')->first();

        $course = Course::first();

        // Create admissions for each instructor
        $admissionAlpha = Admission::create([
            'admission_no' => 'ADM-A-01',
            'roll_no' => 'ROLL-A-01',
            'student_name' => 'Alpha Student',
            'mobile' => '1234567890',
            'course_id' => $course->id,
            'time_slot' => '09:00 AM - 11:00 AM',
            'instructor_id' => $instructorAlpha->id,
            'admission_date' => now()->toDateString(),
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
            'course_id' => $course->id,
            'time_slot' => '11:00 AM - 01:00 PM',
            'instructor_id' => $instructorBeta->id,
            'admission_date' => now()->toDateString(),
            'total_fee' => 1000.00,
            'discount_amount' => 0.00,
            'final_fee' => 1000.00,
            'registration_fee' => 100.00,
            'status' => 'Active',
        ]);

        // Simulating the Eloquent query restriction that would run in AttendanceResource/Policies
        $this->actingAs($instructorAlpha);
        $alphaQuery = Admission::where('instructor_id', auth()->id())->get();
        $this->assertTrue($alphaQuery->contains($admissionAlpha));
        $this->assertFalse($alphaQuery->contains($admissionBeta));

        $this->actingAs($instructorBeta);
        $betaQuery = Admission::where('instructor_id', auth()->id())->get();
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
            'approved_by' => User::role('Accountant')->first()->id,
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
        $admin = User::role('Super Admin')->first();
        $this->actingAs($admin);

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
        $admin = User::role('Super Admin')->first();
        $this->actingAs($admin);

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
        $admin = User::role('Super Admin')->first();
        $this->actingAs($admin);

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
        $admin = User::role('Super Admin')->first();
        $this->actingAs($admin);

        $admission = Admission::first();
        $this->assertNotNull($admission);

        $course2 = Course::where('id', '!=', $admission->course_id)->first();
        $this->assertNotNull($course2);

        $instructor = User::role('Instructor')->first();

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
                        'id' => $admission->id,
                        'course_id' => $admission->course_id,
                        'start_time' => '10:00 AM',
                        'end_time' => '12:00 PM',
                        'time_slot' => '10:00 AM - 12:00 PM',
                        'instructor_id' => $admission->instructor_id,
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

        // Verify that there are now 2 admissions with the edited name and mobile
        $admissions = Admission::where('mobile', $admission->mobile)->get();
        $this->assertCount(2, $admissions);
        $this->assertEquals('John Edited', $admissions[0]->student_name);
        $this->assertEquals('John Edited', $admissions[1]->student_name);
        
        // Verify that the second course was created
        $newCourseAdmission = Admission::where('mobile', $admission->mobile)->where('course_id', $course2->id)->first();
        $this->assertNotNull($newCourseAdmission);
        $this->assertEquals('02:00 PM - 04:00 PM', $newCourseAdmission->time_slot);
        $this->assertEquals(1200, $newCourseAdmission->final_fee);
    }

    /**
     * Test admin user access and deletion restrictions.
     */
    public function test_admin_user_management_and_deletion_restrictions()
    {
        $superAdmin = User::role('Super Admin')->first();
        $admin = User::role('Admin')->first();

        // 1. Admin can viewAny users
        $this->actingAs($admin);
        $this->assertTrue($admin->can('viewAny', User::class));

        // 2. Admin can create users
        $this->assertTrue($admin->can('create', User::class));

        // 3. Admin can update users
        $newUser = User::factory()->create();
        $this->assertTrue($admin->can('update', $newUser));

        // 4. Admin cannot delete a Super Admin
        $this->assertFalse($admin->can('delete', $superAdmin));

        // 5. Super Admin checks return true at policy/gate level due to Gate::before bypass
        $this->assertTrue($superAdmin->can('delete', $superAdmin));

        // Let's create a second Super Admin user to test policy check on another Super Admin
        $secondSuperAdmin = User::factory()->create();
        $secondSuperAdmin->assignRole('Super Admin');
        $this->assertTrue($superAdmin->can('delete', $secondSuperAdmin));

        // 6. Admin can delete a normal user
        $normalUser = User::factory()->create();
        $this->assertTrue($admin->can('delete', $normalUser));
    }

    /**
     * Test that non-Super Admins cannot access roles, permissions, audit trails, and company settings.
     */
    public function test_restricted_modules_access()
    {
        $superAdmin = User::role('Super Admin')->first();
        $admin = User::role('Admin')->first();

        // 1. Roles & Permissions policies
        $this->actingAs($admin);
        $this->assertFalse($admin->can('viewAny', Role::class));
        $this->assertFalse($admin->can('viewAny', Permission::class));

        $this->actingAs($superAdmin);
        $this->assertTrue($superAdmin->can('viewAny', Role::class));
        $this->assertTrue($superAdmin->can('viewAny', Permission::class));

        // 2. Audit Trails page (ActivityLogResource)
        $this->actingAs($admin);
        $this->assertFalse(\App\Filament\Admin\Resources\ActivityLogResource::canViewAny());

        $this->actingAs($superAdmin);
        $this->assertTrue(\App\Filament\Admin\Resources\ActivityLogResource::canViewAny());

        // 3. Company Settings page
        $this->actingAs($admin);
        $this->assertFalse(\App\Filament\Admin\Pages\CompanySettings::canAccess());

        $this->actingAs($superAdmin);
        $this->assertTrue(\App\Filament\Admin\Pages\CompanySettings::canAccess());
    }

    /**
     * Test user deletion restrictions at Filament UI action and bulk action levels.
     */
    public function test_user_deletion_restrictions_via_filament()
    {
        $superAdmin = User::role('Super Admin')->first();
        $admin = User::role('Admin')->first();
        $normalUser = User::factory()->create();

        // 1. Acting as Admin
        $this->actingAs($admin);

        // Admin should not see 'delete' action for a Super Admin row, but should see it for normal users
        \Livewire\Livewire::test(\App\Filament\Admin\Resources\UserResource\Pages\ManageUsers::class)
            ->assertTableActionHidden('delete', $superAdmin)
            ->assertTableActionVisible('delete', $normalUser);

        // 2. Acting as Super Admin
        $this->actingAs($superAdmin);

        // Super Admin should see the action (due to Gate::before bypass), but calling it should halt and NOT delete
        \Livewire\Livewire::test(\App\Filament\Admin\Resources\UserResource\Pages\ManageUsers::class)
            ->callTableAction('delete', $superAdmin);

        $this->assertDatabaseHas('users', ['id' => $superAdmin->id]);

        // Same for another Super Admin
        $secondSuperAdmin = User::factory()->create();
        $secondSuperAdmin->assignRole('Super Admin');

        \Livewire\Livewire::test(\App\Filament\Admin\Resources\UserResource\Pages\ManageUsers::class)
            ->callTableAction('delete', $secondSuperAdmin);

        $this->assertDatabaseHas('users', ['id' => $secondSuperAdmin->id]);

        // Verify that a normal user CAN be deleted by Admin
        $this->actingAs($admin);
        \Livewire\Livewire::test(\App\Filament\Admin\Resources\UserResource\Pages\ManageUsers::class)
            ->callTableAction('delete', $normalUser);

        $this->assertDatabaseMissing('users', ['id' => $normalUser->id]);
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
        $user = User::first();
        $response = $this->actingAs($user)->get('/admin/student-photos/student_photos/avatar.jpg');
        $response->assertStatus(200);
        $this->assertEquals('imagecontent', $response->streamedContent());
    }
}
