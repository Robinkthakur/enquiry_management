<?php

namespace App\Filament\Student\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use App\Filament\Student\Resources\AttendanceResource;

class AttendanceStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $admission = Auth::user()->admission;
        if (!$admission) {
            return [];
        }

        $attendances = $admission->attendances;
        $total = $attendances->count();
        $present = $attendances->where('status', 'Present')->count();
        $absent = $attendances->where('status', 'Absent')->count();
        $leave = $attendances->where('status', 'Leave')->count();

        $percentage = $total > 0 ? round((($present + $leave) / $total) * 100, 1) : 0;

        return [
            Stat::make('Total Classes Conducted', $total)
                ->description('Overall academic sessions scheduled')
                ->color('info')
                ->icon('heroicon-o-academic-cap')
                ->url(AttendanceResource::getUrl('index')),
            Stat::make('Present Days', $present)
                ->description('Successfully attended classes')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->url(AttendanceResource::getUrl('index')),
            Stat::make('Absent / Leave Days', "{$absent} / {$leave}")
                ->description('Missed sessions vs approved leave')
                ->color($absent > 0 ? 'danger' : 'warning')
                ->icon('heroicon-o-x-circle')
                ->url(AttendanceResource::getUrl('index')),
            Stat::make('Attendance Rate', "{$percentage}%")
                ->description('Calculated including approved leaves')
                ->color($percentage >= 75 ? 'success' : 'danger')
                ->icon('heroicon-o-chart-bar')
                ->url(AttendanceResource::getUrl('index')),
        ];
    }
}

