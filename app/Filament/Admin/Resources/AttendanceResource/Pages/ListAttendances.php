<?php

namespace App\Filament\Admin\Resources\AttendanceResource\Pages;

use App\Filament\Admin\Resources\AttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('mark_attendance')
                ->label('Mark Attendance')
                ->color('success')
                ->icon('heroicon-o-plus-circle')
                ->url(fn () => route('filament.admin.pages.mark-attendance')),
            Actions\CreateAction::make()->label('Record Single Attendance'),
        ];
    }
}
