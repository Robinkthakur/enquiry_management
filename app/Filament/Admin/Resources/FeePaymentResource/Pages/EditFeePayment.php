<?php

namespace App\Filament\Admin\Resources\FeePaymentResource\Pages;

use App\Filament\Admin\Resources\FeePaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFeePayment extends EditRecord
{
    protected static string $resource = FeePaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
