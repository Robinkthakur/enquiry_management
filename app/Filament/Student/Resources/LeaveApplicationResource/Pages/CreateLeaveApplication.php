<?php

namespace App\Filament\Student\Resources\LeaveApplicationResource\Pages;

use App\Filament\Student\Resources\LeaveApplicationResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateLeaveApplication extends CreateRecord
{
    protected static string $resource = LeaveApplicationResource::class;

    protected function beforeCreate(): void
    {
        $admission = Auth::user()->admission;
        if ($admission) {
            $exists = \App\Models\LeaveApplication::where('admission_id', $admission->id)
                ->whereIn('status', ['Pending', 'Approved'])
                ->exists();
            if ($exists) {
                \Filament\Notifications\Notification::make()
                    ->title('Cannot apply for leave')
                    ->body('You already have an active or pending leave application.')
                    ->danger()
                    ->send();
                
                $this->halt();
            }
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $admission = Auth::user()->admission;
        if (!$admission) {
            throw new \Exception("Student profile not linked to user.");
        }

        $data['admission_id'] = $admission->id;
        $data['status'] = 'Pending';

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index', panel: 'student');
    }
}
