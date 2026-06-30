<?php

namespace App\Filament\Admin\Resources\LeaveApplicationResource\Pages;

use App\Filament\Admin\Resources\LeaveApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageLeaveApplications extends ManageRecords
{
    protected static string $resource = LeaveApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
