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
                
                // Create installments immediately for this admission
                $this->createInstallmentsFor($admission);
            }
        });
        
        return $firstAdmission;
    }

    protected function createInstallmentsFor(Admission $admission): void
    {
        $regFee = $admission->registration_fee;
        $finalFee = $admission->final_fee;
        
        // Installment 1: Registration Fee due immediately
        FeeInstallment::create([
            'admission_id' => $admission->id,
            'installment_no' => 1,
            'due_date' => $admission->admission_date,
            'amount' => $regFee,
            'paid_amount' => 0.00,
            'due_amount' => $regFee,
            'status' => 'Pending',
        ]);

        // Installment 2 & 3: Split remaining balance into 2 monthly installments
        $remaining = $finalFee - $regFee;
        if ($remaining > 0) {
            $instAmount = round($remaining / 2, 2);
            
            // Installment 2
            FeeInstallment::create([
                'admission_id' => $admission->id,
                'installment_no' => 2,
                'due_date' => date('Y-m-d', strtotime($admission->admission_date . ' + 30 days')),
                'amount' => $instAmount,
                'paid_amount' => 0.00,
                'due_amount' => $instAmount,
                'status' => 'Pending',
            ]);

            // Installment 3 (adjusts for rounding errors)
            FeeInstallment::create([
                'admission_id' => $admission->id,
                'installment_no' => 3,
                'due_date' => date('Y-m-d', strtotime($admission->admission_date . ' + 60 days')),
                'amount' => $remaining - $instAmount,
                'paid_amount' => 0.00,
                'due_amount' => $remaining - $instAmount,
                'status' => 'Pending',
            ]);
        }
    }
}
