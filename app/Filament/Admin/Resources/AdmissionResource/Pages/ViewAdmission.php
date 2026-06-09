<?php

namespace App\Filament\Admin\Resources\AdmissionResource\Pages;

use App\Filament\Admin\Resources\AdmissionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAdmission extends ViewRecord
{
    protected static string $resource = AdmissionResource::class;

    protected string $view = 'filament.admin.resources.admission.view';

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
