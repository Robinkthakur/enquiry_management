<?php

namespace App\Filament\Admin\Resources\AdmissionResource\Pages;

use App\Filament\Admin\Resources\AdmissionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdmissions extends ListRecords
{
    protected static string $resource = AdmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
