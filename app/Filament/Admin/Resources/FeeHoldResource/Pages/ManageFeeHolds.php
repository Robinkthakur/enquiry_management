<?php

namespace App\Filament\Admin\Resources\FeeHoldResource\Pages;

use App\Filament\Admin\Resources\FeeHoldResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageFeeHolds extends ManageRecords
{
    protected static string $resource = FeeHoldResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    $data['approved_by'] = auth()->id() ?? User::first()->id;
                    return $data;
                }),
        ];
    }
}
