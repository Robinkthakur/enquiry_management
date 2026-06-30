<?php

namespace App\Filament\Student\Resources\LeaveApplicationResource\Pages;

use App\Filament\Student\Resources\LeaveApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLeaveApplications extends ListRecords
{
    protected static string $resource = LeaveApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Apply for Leave')
                ->visible(function () {
                    $admissionId = auth()->user()->admission?->id;
                    if (!$admissionId) return false;
                    return !\App\Models\LeaveApplication::where('admission_id', $admissionId)
                        ->whereIn('status', ['Pending', 'Approved'])
                        ->exists();
                }),
        ];
    }
}
