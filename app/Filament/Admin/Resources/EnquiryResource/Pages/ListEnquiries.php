<?php

namespace App\Filament\Admin\Resources\EnquiryResource\Pages;

use App\Filament\Admin\Resources\EnquiryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEnquiries extends ListRecords
{
    protected static string $resource = EnquiryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
