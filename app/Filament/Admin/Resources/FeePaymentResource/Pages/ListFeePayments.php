<?php

namespace App\Filament\Admin\Resources\FeePaymentResource\Pages;

use App\Filament\Admin\Resources\FeePaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFeePayments extends ListRecords
{
    protected static string $resource = FeePaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Record New Payment'),
        ];
    }
}
