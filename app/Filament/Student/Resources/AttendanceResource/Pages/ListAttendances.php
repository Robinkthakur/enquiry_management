<?php

namespace App\Filament\Student\Resources\AttendanceResource\Pages;

use App\Filament\Student\Resources\AttendanceResource;
use App\Filament\Student\Widgets\AttendanceStatsWidget;
use Filament\Resources\Pages\ListRecords;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            AttendanceStatsWidget::class,
        ];
    }
}
