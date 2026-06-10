<?php

namespace App\Filament\Admin\Resources\AdmissionResource\Pages;

use App\Filament\Admin\Resources\AdmissionResource;
use App\Models\Admission;
use App\Models\FeeInstallment;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditAdmission extends EditRecord
{
    protected static string $resource = AdmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()->label('Profile')->icon('heroicon-o-user'),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Find all admissions sharing the same mobile or email to load all courses
        $query = Admission::query();
        if (!empty($data['email'])) {
            $query->where(function ($q) use ($data) {
                $q->where('mobile', $data['mobile'])
                  ->orWhere('email', $data['email']);
            });
        } else {
            $query->where('mobile', $data['mobile']);
        }
        
        $admissions = $query->get();
        
        $data['enrollments'] = $admissions->map(function ($admission) {
            return [
                'id' => $admission->id,
                'course_id' => $admission->course_id,
                'time_slot' => $admission->time_slot,
                'instructor_id' => $admission->instructor_id,
                'total_fee' => $admission->total_fee,
                'registration_fee' => $admission->registration_fee,
                'discount_amount' => $admission->discount_amount,
                'final_fee' => $admission->final_fee,
                'status' => $admission->status,
            ];
        })->toArray();
        
        return $data;
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        $enrollments = $data['enrollments'] ?? [];
        unset($data['enrollments']);
        
        DB::transaction(function () use ($record, $data, $enrollments) {
            // 1. Gather all existing admission IDs for this student (sharing same mobile/email before update)
            $query = Admission::query();
            if (!empty($record->email)) {
                $query->where(function ($q) use ($record) {
                    $q->where('mobile', $record->mobile)
                      ->orWhere('email', $record->email);
                });
            } else {
                $query->where('mobile', $record->mobile);
            }
            $existingAdmissions = $query->get();
            $existingIds = $existingAdmissions->pluck('id')->toArray();
            
            // Track IDs of enrollments we processed
            $processedIds = [];
            
            foreach ($enrollments as $enrollment) {
                $admissionId = $enrollment['id'] ?? null;
                
                $admissionData = array_merge($data, [
                    'course_id' => $enrollment['course_id'],
                    'time_slot' => $enrollment['time_slot'],
                    'instructor_id' => $enrollment['instructor_id'],
                    'total_fee' => $enrollment['total_fee'],
                    'registration_fee' => $enrollment['registration_fee'],
                    'discount_amount' => $enrollment['discount_amount'] ?? 0,
                    'final_fee' => $enrollment['final_fee'],
                    'status' => $enrollment['status'] ?? 'Active',
                ]);
                
                if ($admissionId && in_array($admissionId, $existingIds)) {
                    // Update existing admission
                    $admission = Admission::find($admissionId);
                    $admission->update($admissionData);
                    $processedIds[] = $admissionId;
                } else {
                    // Create new admission (new course enrollment)
                    $admission = Admission::create($admissionData);
                    $processedIds[] = $admission->id;
                }
            }
            
            // Delete any existing admissions that were removed from the repeater
            $deletedIds = array_diff($existingIds, $processedIds);
            if (!empty($deletedIds)) {
                Admission::whereIn('id', $deletedIds)->delete();
            }
            
            // 2. Keep student personal details in sync across all remaining admissions for this student
            $newMobile = $data['mobile'];
            $newEmail = $data['email'] ?? null;
            
            $allQuery = Admission::query();
            if ($newEmail) {
                $allQuery->where('mobile', $newMobile)->orWhere('email', $newEmail);
            } else {
                $allQuery->where('mobile', $newMobile);
            }
            
            $allQuery->update([
                'student_name' => $data['student_name'],
                'father_name' => $data['father_name'] ?? null,
                'mobile' => $newMobile,
                'email' => $newEmail,
                'address' => $data['address'] ?? null,
                'student_photo' => $data['student_photo'] ?? null,
                'admission_date' => $data['admission_date'],
            ]);
        });
        
        return $record->fresh();
    }
}
