<?php

namespace App\Filament\Admin\Resources\FeeInstallmentResource\Pages;

use App\Filament\Admin\Resources\FeeInstallmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFeeInstallment extends EditRecord
{
    protected static string $resource = FeeInstallmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
