<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Course;
use App\Models\Enquiry;
use App\Models\EnquiryTimeline;
use App\Models\Admission;
use App\Models\AdmissionCourse;
use App\Models\FeeInstallment;
use App\Models\FeePayment;
use App\Models\CompanySetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Permissions
        $permissions = [
            'courses.view', 'courses.create', 'courses.update', 'courses.delete',
            'enquiries.view', 'enquiries.create', 'enquiries.update', 'enquiries.delete',
            'admissions.view', 'admissions.create', 'admissions.update', 'admissions.delete',
            'attendance.view', 'attendance.manage',
            'fees.view', 'fees.manage',
            'holds.view', 'holds.manage',
            'certificates.view', 'certificates.manage',
            'reports.view',
            'roles.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // 2. Create Roles
        $superAdminRole = Role::findOrCreate('Super Admin', 'web');
        $adminRole = Role::findOrCreate('Admin', 'web');
        $counselorRole = Role::findOrCreate('Counselor', 'web');
        $accountantRole = Role::findOrCreate('Accountant', 'web');
        $instructorRole = Role::findOrCreate('Instructor', 'web');

        // Assign Permissions to Roles
        // Admin permissions: all except roles management
        $adminRole->syncPermissions(array_filter($permissions, fn($p) => $p !== 'roles.manage'));

        // Counselor permissions
        $counselorRole->syncPermissions([
            'courses.view',
            'enquiries.view', 'enquiries.create', 'enquiries.update',
            'admissions.view', 'admissions.create', 'admissions.update',
        ]);

        // Accountant permissions
        $accountantRole->syncPermissions([
            'courses.view',
            'admissions.view',
            'fees.view', 'fees.manage',
            'holds.view', 'holds.manage',
            'reports.view',
        ]);

        // Instructor permissions
        $instructorRole->syncPermissions([
            'courses.view',
            'attendance.view', 'attendance.manage',
        ]);

        // 3. Create Default Users
        $usersData = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@sms.com',
                'password' => Hash::make('password'),
                'role' => 'Super Admin'
            ],
            [
                'name' => 'Admin User',
                'email' => 'admin@sms.com',
                'password' => Hash::make('password'),
                'role' => 'Admin'
            ],
            [
                'name' => 'Counselor User',
                'email' => 'counselor@sms.com',
                'password' => Hash::make('password'),
                'role' => 'Counselor'
            ],
            [
                'name' => 'Accountant User',
                'email' => 'accountant@sms.com',
                'password' => Hash::make('password'),
                'role' => 'Accountant'
            ],
            [
                'name' => 'Instructor Alpha',
                'email' => 'instructor@sms.com',
                'password' => Hash::make('password'),
                'role' => 'Instructor'
            ],
            [
                'name' => 'Instructor Beta',
                'email' => 'instructor2@sms.com',
                'password' => Hash::make('password'),
                'role' => 'Instructor'
            ]
        ];

        $createdUsers = [];
        foreach ($usersData as $data) {
            $user = \App\Models\Admin::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => $data['password'],
                ]
            );
            $user->assignRole($data['role']);
            $createdUsers[$data['role']][] = $user;
        }

        // Keep direct handle to counselor and instructor users for seeding relations
        $counselor = $createdUsers['Counselor'][0];
        $instructorAlpha = $createdUsers['Instructor'][0];
        $instructorBeta = $createdUsers['Instructor'][1];

        // 4. Seed Courses
        $coursesData = [
            [
                'course_code' => 'FSWD',
                'course_name' => 'Full Stack Web Development',
                'description' => 'Comprehensive training in HTML, CSS, JavaScript, PHP, Laravel, and Vue/React.',
                'duration_months' => 6,
                'total_fee' => 1500.00,
                'registration_fee' => 150.00,
                'certificate_fee' => 50.00,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'course_code' => 'DSML',
                'course_name' => 'Data Science & Machine Learning',
                'description' => 'Learn Python, SQL, Pandas, NumPy, Scikit-Learn, and Deep Learning.',
                'duration_months' => 8,
                'total_fee' => 2200.00,
                'registration_fee' => 200.00,
                'certificate_fee' => 75.00,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'course_code' => 'UIUX',
                'course_name' => 'UI/UX Design Masterclass',
                'description' => 'Master Figma, wireframing, prototyping, user testing, and visual design.',
                'duration_months' => 4,
                'total_fee' => 1000.00,
                'registration_fee' => 100.00,
                'certificate_fee' => 50.00,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ]
        ];

        $courses = [];
        foreach ($coursesData as $c) {
            $courses[] = Course::create($c);
        }

        // 5. Seed Batches (Skipped - removed batch system)

        // 6. Seed Enquiries
        $enquiriesData = [
            [
                'enquiry_no' => 'ENQ-2026-00001',
                'name' => 'John Doe',
                'father_name' => 'Richard Doe',
                'mobile' => '+15550199',
                'email' => 'john.doe@example.com',
                'gender' => 'Male',
                'date_of_birth' => '1998-05-15',
                'qualification' => 'Bachelor of Science',
                'occupation' => 'Student',
                'address' => '123 Tech Lane, Silicon Valley',
                'enquiry_source' => 'Google Search',
                'remarks' => 'Very interested in Laravel and Vue.',
                'follow_up_date' => '2026-06-05',
                'taken_by' => $counselor->id,
                'status' => 'Follow Up',
            ],
            [
                'enquiry_no' => 'ENQ-2026-00002',
                'name' => 'Jane Smith',
                'father_name' => 'William Smith',
                'mobile' => '+15550288',
                'email' => 'jane.smith@example.com',
                'gender' => 'Female',
                'date_of_birth' => '1999-11-20',
                'qualification' => 'High School',
                'occupation' => 'Freelancer',
                'address' => '456 Design Road, Creativesville',
                'enquiry_source' => 'Facebook Ad',
                'remarks' => 'Looking for visual portfolio design advice.',
                'follow_up_date' => '2026-06-04',
                'taken_by' => $counselor->id,
                'status' => 'New',
            ],
            [
                'enquiry_no' => 'ENQ-2026-00003',
                'name' => 'Robert Johnson',
                'father_name' => 'David Johnson',
                'mobile' => '+15550377',
                'email' => 'robert.j@example.com',
                'gender' => 'Male',
                'date_of_birth' => '1995-03-10',
                'qualification' => 'Master of Computer Applications',
                'occupation' => 'Software Intern',
                'address' => '789 Data Parkway, Analyst City',
                'enquiry_source' => 'Referral',
                'remarks' => 'Ready to enroll. Already completed basic Python.',
                'follow_up_date' => '2026-06-03',
                'taken_by' => $counselor->id,
                'status' => 'Admitted',
            ]
        ];

        $enquiries = [];
        foreach ($enquiriesData as $idx => $enq) {
            $enquiry = Enquiry::create($enq);
            // Link to courses
            if ($idx === 0) {
                $enquiry->interestedCourses()->attach([$courses[0]->id, $courses[2]->id]);
            } else if ($idx === 1) {
                $enquiry->interestedCourses()->attach([$courses[2]->id]);
            } else {
                $enquiry->interestedCourses()->attach([$courses[1]->id]);
            }
            $enquiries[] = $enquiry;

            // Seed timeline
            EnquiryTimeline::create([
                'enquiry_id' => $enquiry->id,
                'user_id' => $counselor->id,
                'status_from' => null,
                'status_to' => $enquiry->status,
                'notes' => 'Initial Enquiry Recorded: ' . $enquiry->remarks,
                'follow_up_date' => $enquiry->follow_up_date,
            ]);
        }

        // 7. Seed Admission for Robert Johnson (ENQ-2026-00003)
        $admittedEnquiry = $enquiries[2];
        $course = $courses[1]; // DSML

        $admission = Admission::create([
            'admission_no' => 'ADM-2026-00001',
            'enquiry_id' => $admittedEnquiry->id,
            'roll_no' => 'ROLL-2026-00001',
            'student_name' => $admittedEnquiry->name,
            'father_name' => $admittedEnquiry->father_name,
            'mobile' => $admittedEnquiry->mobile,
            'email' => $admittedEnquiry->email,
            'address' => $admittedEnquiry->address,
            'admission_date' => '2026-06-03',
            'status' => 'Active',
        ]);

        $enrollment = AdmissionCourse::create([
            'admission_id' => $admission->id,
            'course_id' => $course->id,
            'time_slot' => '06:00 PM - 08:00 PM',
            'instructor_id' => $instructorBeta->id,
            'total_fee' => $course->total_fee + $course->registration_fee,
            'discount_amount' => 100.00,
            'final_fee' => ($course->total_fee + $course->registration_fee) - 100.00,
            'registration_fee' => $course->registration_fee,
            'status' => 'Active',
        ]);

        // Generate Installments for Robert:
        // Installment 1: Registration Fee due immediately (Paid)
        $inst1 = FeeInstallment::create([
            'admission_id' => $admission->id,
            'admission_course_id' => $enrollment->id,
            'installment_no' => 1,
            'due_date' => '2026-06-03',
            'amount' => $course->registration_fee,
            'paid_amount' => $course->registration_fee,
            'due_amount' => 0.00,
            'status' => 'Paid',
        ]);

        // Installment 2: Half of remaining final fee due in 1 month (Pending)
        $remainingFee = $enrollment->final_fee - $course->registration_fee;
        $inst2 = FeeInstallment::create([
            'admission_id' => $admission->id,
            'admission_course_id' => $enrollment->id,
            'installment_no' => 2,
            'due_date' => '2026-07-03',
            'amount' => $remainingFee / 2,
            'paid_amount' => 0.00,
            'due_amount' => $remainingFee / 2,
            'status' => 'Pending',
        ]);

        // Installment 3: Other half due in 2 months (Pending)
        $inst3 = FeeInstallment::create([
            'admission_id' => $admission->id,
            'admission_course_id' => $enrollment->id,
            'installment_no' => 3,
            'due_date' => '2026-08-03',
            'amount' => $remainingFee / 2,
            'paid_amount' => 0.00,
            'due_amount' => $remainingFee / 2,
            'status' => 'Pending',
        ]);

        // Create Payment receipt for Installment 1
        FeePayment::create([
            'receipt_no' => 'RCPT-2026-00001',
            'admission_id' => $admission->id,
            'fee_installment_id' => $inst1->id,
            'amount_paid' => $course->registration_fee,
            'payment_method' => 'UPI',
            'transaction_reference' => 'TXN9988776655',
            'receipt_date' => '2026-06-03',
        ]);

        // Seed Company settings
        CompanySetting::create([
            'company_name' => 'EDU INSTITUTE',
            'description' => 'Empowering Minds, Shaping Futures',
            'support_email' => 'support@eduinstitute.com',
            'mobile_no' => '+1 555-0199',
            'website' => 'https://www.eduinstitute.com',
            'address' => '123 Tech Lane, Silicon Valley',
            'logo' => null,
        ]);
    }
}
