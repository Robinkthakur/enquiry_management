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
        $admission = $this->getRecord();
        $data['enrollments'] = $admission->enrollments->map(function ($enrollment) {
            return [
                'id' => $enrollment->id,
                'course_id' => $enrollment->course_id,
                'time_slot' => $enrollment->time_slot,
                'instructor_id' => $enrollment->instructor_id,
                'total_fee' => $enrollment->total_fee,
                'registration_fee' => $enrollment->registration_fee,
                'discount_amount' => $enrollment->discount_amount,
                'final_fee' => $enrollment->final_fee,
                'status' => $enrollment->status,
            ];
        })->toArray();
        
        return $data;
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        $enrollments = $data['enrollments'] ?? [];
        unset($data['enrollments']);
        
        DB::transaction(function () use ($record, $data, $enrollments) {
            // 1. Update the student personal details on the main Admission record
            $record->update($data);
            
            // 2. Track processed enrollment IDs to delete any that were removed
            $processedIds = [];
            
            foreach ($enrollments as $enrollment) {
                $enrollmentId = $enrollment['id'] ?? null;
                
                $enrollmentData = [
                    'course_id' => $enrollment['course_id'],
                    'time_slot' => $enrollment['time_slot'] ?? null,
                    'instructor_id' => $enrollment['instructor_id'] ?? null,
                    'total_fee' => $enrollment['total_fee'],
                    'registration_fee' => $enrollment['registration_fee'] ?? 0.00,
                    'discount_amount' => $enrollment['discount_amount'] ?? 0.00,
                    'final_fee' => $enrollment['final_fee'],
                    'status' => $enrollment['status'] ?? 'Active',
                ];
                
                if ($enrollmentId) {
                    $existingEnrollment = $record->enrollments()->find($enrollmentId);
                    if ($existingEnrollment) {
                        $existingEnrollment->update($enrollmentData);
                        $processedIds[] = $enrollmentId;
                    }
                } else {
                    $newEnrollment = $record->enrollments()->create($enrollmentData);
                    $processedIds[] = $newEnrollment->id;
                }
            }
            
            // Delete any enrollments that were removed from the repeater
            $record->enrollments()->whereNotIn('id', $processedIds)->delete();
        });
        
        return $record->fresh();
    }
}
