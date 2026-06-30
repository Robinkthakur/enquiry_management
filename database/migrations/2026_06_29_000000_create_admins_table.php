<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create admins table
        Schema::create('admins', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        // 2. Identify admin users (users who do not have Spatie Student role)
        $studentRole = DB::table('roles')->where('name', 'Student')->first();
        $studentUserIds = [];
        if ($studentRole) {
            $studentUserIds = DB::table('model_has_roles')
                ->where('role_id', $studentRole->id)
                ->where('model_type', 'App\Models\User')
                ->pluck('model_id')
                ->toArray();
        }

        // Also get user_ids from admissions table
        $admissionsUserIds = DB::table('admissions')->whereNotNull('user_id')->pluck('user_id')->toArray();
        $allStudentIds = array_unique(array_merge($studentUserIds, $admissionsUserIds));

        $adminUsers = DB::table('users')->whereNotIn('id', $allStudentIds)->get();

        // 3. Move admin users data to admins table
        foreach ($adminUsers as $admin) {
            DB::table('admins')->insert([
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
                'email_verified_at' => $admin->email_verified_at,
                'password' => $admin->password,
                'remember_token' => $admin->remember_token,
                'created_at' => $admin->created_at ?? now(),
                'updated_at' => $admin->updated_at ?? now(),
            ]);
        }

        // 4. Update Spatie model_has_roles and model_has_permissions
        $adminUserIds = $adminUsers->pluck('id')->toArray();
        if (!empty($adminUserIds)) {
            DB::table('model_has_roles')
                ->whereIn('model_id', $adminUserIds)
                ->where('model_type', 'App\Models\User')
                ->update(['model_type' => 'App\Models\Admin']);

            DB::table('model_has_permissions')
                ->whereIn('model_id', $adminUserIds)
                ->where('model_type', 'App\Models\User')
                ->update(['model_type' => 'App\Models\Admin']);
        }

        // 5. Delete admin users from users table
        if (!empty($adminUserIds)) {
            DB::table('users')->whereIn('id', $adminUserIds)->delete();
        }

        // 6. Update foreign keys
        // Drop foreign keys pointing to users
        Schema::table('enquiries', function (Blueprint $table) {
            $table->dropForeign(['taken_by']);
        });

        Schema::table('enquiry_timeline', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('fee_holds', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
        });

        Schema::table('admission_courses', function (Blueprint $table) {
            $table->dropForeign(['instructor_id']);
        });

        // Add new foreign keys pointing to admins
        Schema::table('enquiries', function (Blueprint $table) {
            $table->foreign('taken_by')->references('id')->on('admins')->cascadeOnDelete();
        });

        Schema::table('enquiry_timeline', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('admins')->cascadeOnDelete();
        });

        Schema::table('fee_holds', function (Blueprint $table) {
            $table->foreign('approved_by')->references('id')->on('admins')->cascadeOnDelete();
        });

        Schema::table('admission_courses', function (Blueprint $table) {
            $table->foreign('instructor_id')->references('id')->on('admins')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign keys pointing to admins
        Schema::table('enquiries', function (Blueprint $table) {
            $table->dropForeign(['taken_by']);
        });

        Schema::table('enquiry_timeline', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('fee_holds', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
        });

        Schema::table('admission_courses', function (Blueprint $table) {
            $table->dropForeign(['instructor_id']);
        });

        // Move admins back to users
        $admins = DB::table('admins')->get();
        foreach ($admins as $admin) {
            DB::table('users')->insertOrIgnore([
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
                'email_verified_at' => $admin->email_verified_at,
                'password' => $admin->password,
                'remember_token' => $admin->remember_token,
                'created_at' => $admin->created_at,
                'updated_at' => $admin->updated_at,
            ]);
        }

        // Restore Spatie roles/permissions type
        $adminIds = $admins->pluck('id')->toArray();
        if (!empty($adminIds)) {
            DB::table('model_has_roles')
                ->whereIn('model_id', $adminIds)
                ->where('model_type', 'App\Models\Admin')
                ->update(['model_type' => 'App\Models\User']);

            DB::table('model_has_permissions')
                ->whereIn('model_id', $adminIds)
                ->where('model_type', 'App\Models\Admin')
                ->update(['model_type' => 'App\Models\User']);
        }

        // Re-create foreign keys pointing to users
        Schema::table('enquiries', function (Blueprint $table) {
            $table->foreign('taken_by')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('enquiry_timeline', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('fee_holds', function (Blueprint $table) {
            $table->foreign('approved_by')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('admission_courses', function (Blueprint $table) {
            $table->foreign('instructor_id')->references('id')->on('users')->nullOnDelete();
        });

        // Drop admins table
        Schema::dropIfExists('admins');
    }
};
