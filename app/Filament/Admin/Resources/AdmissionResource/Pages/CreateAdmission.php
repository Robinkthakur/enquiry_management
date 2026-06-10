<?php

namespace App\Filament\Admin\Resources\AdmissionResource\Pages;

use App\Filament\Admin\Resources\AdmissionResource;
use App\Models\Admission;
use App\Models\FeeInstallment;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateAdmission extends CreateRecord
{
    protected static string $resource = AdmissionResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $enrollments = $data['enrollments'] ?? [];
        unset($data['enrollments']);
        
        $firstAdmission = null;
        
        DB::transaction(function () use ($data, $enrollments, &$firstAdmission) {
            foreach ($enrollments as $index => $enrollment) {
                // Merge student details with enrollment details
                $admissionData = array_merge($data, [
                    'course_id' => $enrollment['course_id'],
                    'time_slot' => $enrollment['time_slot'] ?? null,
                    'instructor_id' => $enrollment['instructor_id'] ?? null,
                    'total_fee' => $enrollment['total_fee'],
                    'registration_fee' => $enrollment['registration_fee'],
                    'discount_amount' => $enrollment['discount_amount'] ?? 0,
                    'final_fee' => $enrollment['final_fee'],
                    'status' => $enrollment['status'] ?? 'Active',
                ]);
                
                $admission = Admission::create($admissionData);
                
                if ($index === 0) {
                    $firstAdmission = $admission;
                }
            }
        });
        
        return $firstAdmission;
    }
}
