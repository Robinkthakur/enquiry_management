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
        
        $admission = null;
        
        DB::transaction(function () use ($data, $enrollments, &$admission) {
            $admission = Admission::create($data);
            
            foreach ($enrollments as $enrollment) {
                $admission->enrollments()->create([
                    'course_id' => $enrollment['course_id'],
                    'time_slot' => $enrollment['time_slot'] ?? null,
                    'instructor_id' => $enrollment['instructor_id'] ?? null,
                    'total_fee' => $enrollment['total_fee'],
                    'registration_fee' => $enrollment['registration_fee'] ?? 0.00,
                    'discount_amount' => $enrollment['discount_amount'] ?? 0.00,
                    'final_fee' => $enrollment['final_fee'],
                    'status' => $enrollment['status'] ?? 'Active',
                ]);
            }
        });
        
        return $admission;
    }
}
