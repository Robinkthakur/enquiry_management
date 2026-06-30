<?php

namespace App\Filament\Student\Resources\LeaveApplicationResource\Pages;

use App\Filament\Student\Resources\LeaveApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLeaveApplication extends EditRecord
{
    protected static string $resource = LeaveApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index', panel: 'student');
    }
}
