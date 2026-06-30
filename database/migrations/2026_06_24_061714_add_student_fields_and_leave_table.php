<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add fields to admissions table
        Schema::table('admissions', function (Blueprint $table) {
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->json('documents')->nullable();
        });

        // 2. Create leave_applications table
        Schema::create('leave_applications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('admission_id')->constrained('admissions')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->text('reason');
            $table->string('status')->default('Pending'); // Pending, Approved, Rejected
            $table->text('admin_remarks')->nullable();
            $table->timestamps();
        });

        // 3. Ensure Spatie Role 'Student' exists and link existing admissions
        if (Schema::hasTable('roles')) {
            Role::findOrCreate('Student', 'web');

            // Link existing admissions to users
            $admissions = DB::table('admissions')->get();
            foreach ($admissions as $admission) {
                $email = $admission->email ?: strtolower($admission->admission_no) . '@student.sms.com';
                
                // Check if user already exists
                $userId = DB::table('users')->where('email', $email)->value('id');
                if (!$userId) {
                    $userId = (string) Str::uuid();
                    DB::table('users')->insert([
                        'id' => $userId,
                        'name' => $admission->student_name,
                        'email' => $email,
                        'password' => Hash::make('password'),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                
                // Assign Student role (using Spatie tables)
                $roleId = DB::table('roles')->where('name', 'Student')->value('id');
                if ($roleId) {
                    $hasRole = DB::table('model_has_roles')
                        ->where('role_id', $roleId)
                        ->where('model_id', $userId)
                        ->where('model_type', 'App\Models\User')
                        ->exists();
                    if (!$hasRole) {
                        DB::table('model_has_roles')->insert([
                            'role_id' => $roleId,
                            'model_type' => 'App\Models\User',
                            'model_id' => $userId,
                        ]);
                    }
                }
                
                DB::table('admissions')->where('id', $admission->id)->update([
                    'user_id' => $userId,
                    'email' => $email,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_applications');

        Schema::table('admissions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'documents']);
        });

        if (Schema::hasTable('roles')) {
            Role::where('name', 'Student')->where('guard_name', 'web')->delete();
        }
    }
};
