<?php

namespace App\Filament\Student\Resources\FeeInstallmentResource\Pages;

use App\Filament\Student\Resources\FeeInstallmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFeeInstallments extends ListRecords
{
    protected static string $resource = FeeInstallmentResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
