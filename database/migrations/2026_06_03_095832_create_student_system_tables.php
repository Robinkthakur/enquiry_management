<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Courses Table
        Schema::create('courses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('course_code')->unique();
            $table->string('course_name');
            $table->text('description')->nullable();
            $table->integer('duration_months');
            $table->decimal('total_fee', 10, 2);
            $table->decimal('registration_fee', 10, 2);
            $table->decimal('certificate_fee', 10, 2);
            $table->decimal('tax_percentage', 5, 2)->default(0.00);
            $table->string('status')->default('active'); // active, inactive
            $table->softDeletes();
            $table->timestamps();
        });

        // 2. Enquiries Table
        Schema::create('enquiries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('enquiry_no')->unique();
            $table->string('name');
            $table->string('father_name')->nullable();
            $table->string('mobile');
            $table->string('email')->nullable();
            $table->string('gender')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('qualification')->nullable();
            $table->string('occupation')->nullable();
            $table->text('address')->nullable();
            $table->string('enquiry_source'); // Google, referral, etc.
            $table->text('remarks')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->foreignUuid('taken_by')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('New'); // New, Follow Up, Interested, Not Interested, Admitted
            $table->softDeletes();
            $table->timestamps();
        });

        // 3. Enquiry Courses Pivot Table
        Schema::create('enquiry_courses', function (Blueprint $table) {
            $table->foreignUuid('enquiry_id')->constrained('enquiries')->cascadeOnDelete();
            $table->foreignUuid('course_id')->constrained('courses')->cascadeOnDelete();
            $table->primary(['enquiry_id', 'course_id']);
        });

        // 4. Enquiry Timeline Table
        Schema::create('enquiry_timeline', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('enquiry_id')->constrained('enquiries')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('status_from')->nullable();
            $table->string('status_to');
            $table->text('notes')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->timestamps();
        });

        // 6. Admissions (Students) Table
        Schema::create('admissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('admission_no')->unique();
            $table->foreignUuid('enquiry_id')->nullable()->constrained('enquiries')->nullOnDelete();
            $table->string('student_photo')->nullable();
            $table->string('roll_no')->unique();
            $table->string('student_name');
            $table->string('father_name')->nullable();
            $table->string('mobile');
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->foreignUuid('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('time_slot')->nullable();
            $table->foreignUuid('instructor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('admission_date');
            $table->decimal('total_fee', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('final_fee', 10, 2);
            $table->decimal('registration_fee', 10, 2);
            $table->string('status')->default('Active'); // Active, Hold, Completed, Cancelled
            $table->softDeletes();
            $table->timestamps();
        });

        // 7. Attendances Table
        Schema::create('attendances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('admission_id')->constrained('admissions')->cascadeOnDelete();
            $table->date('attendance_date');
            $table->string('status'); // Present, Absent, Leave
            $table->unique(['admission_id', 'attendance_date'], 'student_date_unique');
            $table->timestamps();
        });

        // 8. Fee Installments Table
        Schema::create('fee_installments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('admission_id')->constrained('admissions')->cascadeOnDelete();
            $table->integer('installment_no');
            $table->date('due_date');
            $table->decimal('amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0.00);
            $table->decimal('due_amount', 10, 2);
            $table->string('status')->default('Pending'); // Pending, Paid, Partial, Overdue, Hold
            $table->softDeletes();
            $table->timestamps();
        });

        // 9. Fee Payments Table
        Schema::create('fee_payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('receipt_no')->unique();
            $table->foreignUuid('admission_id')->constrained('admissions')->cascadeOnDelete();
            $table->foreignUuid('fee_installment_id')->nullable()->constrained('fee_installments')->nullOnDelete();
            $table->decimal('amount_paid', 10, 2);
            $table->string('payment_method'); // Cash, UPI, Card, Bank Transfer
            $table->string('transaction_reference')->nullable();
            $table->date('receipt_date');
            $table->timestamps();
        });

        // 10. Fee Holds Table
        Schema::create('fee_holds', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('admission_id')->constrained('admissions')->cascadeOnDelete();
            $table->date('hold_from');
            $table->date('hold_to')->nullable();
            $table->text('reason');
            $table->foreignUuid('approved_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // 11. Certificates Table
        Schema::create('certificates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('certificate_no')->unique();
            $table->foreignUuid('admission_id')->constrained('admissions')->cascadeOnDelete();
            $table->foreignUuid('course_id')->constrained('courses')->cascadeOnDelete();
            $table->date('issue_date');
            $table->date('completion_date');
            $table->string('verification_token')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('fee_holds');
        Schema::dropIfExists('fee_payments');
        Schema::dropIfExists('fee_installments');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('admissions');
        Schema::dropIfExists('enquiry_timeline');
        Schema::dropIfExists('enquiry_courses');
        Schema::dropIfExists('enquiries');
        Schema::dropIfExists('courses');
    }
};
