<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImportDatabaseSqlSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sqlPath = base_path('database.sql');
        if (!file_exists($sqlPath)) {
            $this->command->error("database.sql file not found at {$sqlPath}");
            return;
        }

        $this->command->info("Clearing current database tables...");
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $tablesToClear = [
            'admins',
            'activity_log',
            'attendances',
            'certificates',
            'enquiry_timeline',
            'enquiry_courses',
            'enquiries',
            'fee_holds',
            'fee_payments',
            'fee_installments',
            'admission_courses',
            'admissions',
            'courses',
            'company_settings',
            'model_has_permissions',
            'model_has_roles',
            'role_has_permissions',
            'roles',
            'permissions',
            'users',
            'sessions',
            'cache',
            'cache_locks'
        ];

        foreach ($tablesToClear as $table) {
            DB::table($table)->truncate();
        }

        $this->command->info("Reading and executing database.sql inserts...");

        $handle = fopen($sqlPath, "r");
        if ($handle) {
            $queryBuffer = '';
            $insertCount = 0;
            
            while (($line = fgets($handle)) !== false) {
                $trimmedLine = trim($line);
                
                // Skip empty lines and SQL comments
                if ($trimmedLine === '' || str_starts_with($trimmedLine, '--') || str_starts_with($trimmedLine, '/*')) {
                    continue;
                }
                
                $queryBuffer .= $line;
                
                // Check if statement ends with semicolon
                if (str_ends_with($trimmedLine, ';')) {
                    $query = trim($queryBuffer);
                    
                    // Match INSERT INTO statements
                    if (preg_match('/^\s*INSERT\s+INTO\s+`([^`]+)`/i', $query, $matches)) {
                        $tableName = $matches[1];
                        
                        // Skip migrations table to avoid conflicting with current fresh migrations state
                        if ($tableName !== 'migrations') {
                            DB::unprepared($query);
                            $insertCount++;
                        }
                    }
                    
                    $queryBuffer = '';
                }
            }
            
            fclose($handle);
            $this->command->info("Executed {$insertCount} INSERT statements successfully.");
        }

        $this->command->info("Separating admin users and updating roles...");
        
        // Identify admin users (users who do not have Spatie Student role)
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

        // Move admin users data to admins table
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

        // Update Spatie model_has_roles and model_has_permissions
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

        // Delete admin users from users table
        if (!empty($adminUserIds)) {
            DB::table('users')->whereIn('id', $adminUserIds)->delete();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->command->info("Database seeded from database.sql successfully!");
    }
}
