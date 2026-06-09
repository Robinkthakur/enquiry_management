<?php

namespace App\Filament\Admin\Resources\ActivityLogResource\Pages;

use App\Filament\Admin\Resources\ActivityLogResource;
use Filament\Resources\Pages\ManageRecords;

class ManageActivityLogs extends ManageRecords
{
    protected static string $resource = ActivityLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Read-only
        ];
    }
}
