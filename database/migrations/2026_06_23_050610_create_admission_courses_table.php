<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create admission_courses table
        Schema::create('admission_courses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('admission_id')->constrained('admissions')->cascadeOnDelete();
            $table->foreignUuid('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('time_slot')->nullable();
            $table->foreignUuid('instructor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('total_fee', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('final_fee', 10, 2);
            $table->decimal('registration_fee', 10, 2)->default(0.00);
            $table->string('status')->default('Active'); // Active, Hold, Completed, Cancelled
            $table->softDeletes();
            $table->timestamps();
        });

        // 2. Add columns to fee_installments and attendances
        Schema::table('fee_installments', function (Blueprint $table) {
            $table->foreignUuid('admission_course_id')->nullable()->after('admission_id')->constrained('admission_courses')->cascadeOnDelete();
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->foreignUuid('admission_course_id')->nullable()->after('admission_id')->constrained('admission_courses')->cascadeOnDelete();
        });

        // 3. Migrate existing data
        $admissions = DB::table('admissions')->get();
        foreach ($admissions as $admission) {
            if ($admission->course_id) {
                $enrollmentId = (string) Str::uuid();
                
                // Insert into admission_courses
                DB::table('admission_courses')->insert([
                    'id' => $enrollmentId,
                    'admission_id' => $admission->id,
                    'course_id' => $admission->course_id,
                    'time_slot' => $admission->time_slot,
                    'instructor_id' => $admission->instructor_id,
                    'total_fee' => $admission->total_fee,
                    'discount_amount' => $admission->discount_amount,
                    'final_fee' => $admission->final_fee,
                    'registration_fee' => $admission->registration_fee,
                    'status' => $admission->status,
                    'created_at' => $admission->created_at ?? now(),
                    'updated_at' => $admission->updated_at ?? now(),
                ]);

                // Update Fee Installments to point to this enrollment
                DB::table('fee_installments')
                    ->where('admission_id', $admission->id)
                    ->update(['admission_course_id' => $enrollmentId]);

                // Update Attendances to point to this enrollment
                DB::table('attendances')
                    ->where('admission_id', $admission->id)
                    ->update(['admission_course_id' => $enrollmentId]);
            }
        }

        // 4. Update constraints on attendances table
        Schema::table('attendances', function (Blueprint $table) {
            // Add regular index on admission_id to satisfy the foreign key constraint
            $table->index('admission_id', 'attendances_admission_id_index');
            // Drop old unique constraint
            $table->dropUnique('student_date_unique');
            // Add new unique constraint
            $table->unique(['admission_course_id', 'attendance_date'], 'enrollment_date_unique');
        });

        // 5. Drop columns from admissions table
        Schema::table('admissions', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->dropForeign(['instructor_id']);
            $table->dropColumn([
                'course_id',
                'time_slot',
                'instructor_id',
                'total_fee',
                'discount_amount',
                'final_fee',
                'registration_fee'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-add columns to admissions table
        Schema::table('admissions', function (Blueprint $table) {
            $table->foreignUuid('course_id')->nullable()->constrained('courses')->cascadeOnDelete();
            $table->string('time_slot')->nullable();
            $table->foreignUuid('instructor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('total_fee', 10, 2)->default(0.00);
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('final_fee', 10, 2)->default(0.00);
            $table->decimal('registration_fee', 10, 2)->default(0.00);
        });

        // Restore data from admission_courses to admissions table
        $enrollments = DB::table('admission_courses')->get();
        foreach ($enrollments as $enrollment) {
            DB::table('admissions')
                ->where('id', $enrollment->admission_id)
                ->update([
                    'course_id' => $enrollment->course_id,
                    'time_slot' => $enrollment->time_slot,
                    'instructor_id' => $enrollment->instructor_id,
                    'total_fee' => $enrollment->total_fee,
                    'discount_amount' => $enrollment->discount_amount,
                    'final_fee' => $enrollment->final_fee,
                    'registration_fee' => $enrollment->registration_fee,
                ]);
        }

        // Drop unique constraint on attendances
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropUnique('enrollment_date_unique');
        });

        // Drop columns from fee_installments and attendances
        Schema::table('fee_installments', function (Blueprint $table) {
            $table->dropForeign(['admission_course_id']);
            $table->dropColumn('admission_course_id');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['admission_course_id']);
            $table->dropColumn('admission_course_id');
            // Re-add unique constraint
            $table->unique(['admission_id', 'attendance_date'], 'student_date_unique');
            // Drop regular index
            $table->dropIndex('attendances_admission_id_index');
        });

        // Drop admission_courses table
        Schema::dropIfExists('admission_courses');
    }
};
