<?php

namespace App\Filament\Student\Resources\EnrollmentResource\Pages;

use App\Filament\Student\Resources\EnrollmentResource;
use App\Models\Course;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateEnrollment extends CreateRecord
{
    protected static string $resource = EnrollmentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $admission = Auth::user()->admission;
        if (!$admission) {
            throw new \Exception("Student profile not linked to user.");
        }

        $course = Course::find($data['course_id']);
        if (!$course) {
            throw new \Exception("Selected course not found.");
        }

        $data['admission_id'] = $admission->id;
        $data['total_fee'] = $course->total_fee;
        $data['discount_amount'] = 0.00;
        $data['final_fee'] = $course->total_fee;
        $data['registration_fee'] = $course->registration_fee;
        $data['status'] = 'Active';

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index', panel: 'student');
    }
}
