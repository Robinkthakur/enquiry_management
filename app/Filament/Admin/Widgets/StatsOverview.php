<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Enquiry;
use App\Models\Admission;
use App\Models\FeeInstallment;
use App\Models\FeePayment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $today = now()->toDateString();
        $startOfMonth = now()->startOfMonth()->toDateString();
        $endOfMonth = now()->endOfMonth()->toDateString();

        // 1. Enquiries
        $totalEnquiries = Enquiry::count();
        $todaysEnquiries = Enquiry::whereDate('created_at', $today)->count();

        // 2. Admissions
        $admissionsThisMonth = Admission::whereBetween('admission_date', [$startOfMonth, $endOfMonth])->count();
        $activeStudents = Admission::where('status', 'Active')->count();

        // 3. Fees
        $dueFees = FeeInstallment::whereIn('status', ['Pending', 'Partial', 'Overdue'])->sum('due_amount');
        $todaysCollection = FeePayment::whereDate('receipt_date', $today)->sum('amount_paid');
        $monthlyCollection = FeePayment::whereBetween('receipt_date', [$startOfMonth, $endOfMonth])->sum('amount_paid');

        // 4. Attendance
        $students = Admission::where('status', 'Active')->get();
        $avgAttendance = $students->isNotEmpty() ? round($students->avg('attendance_percentage'), 1) : 0.0;

        // 5. Upcoming Installments (7 days)
        $upcomingInstallments = FeeInstallment::whereIn('status', ['Pending', 'Partial'])
            ->whereBetween('due_date', [$today, now()->addDays(7)->toDateString()])
            ->count();

        return [
            Stat::make('Total Enquiries', $totalEnquiries)
                ->description("Today's: {$todaysEnquiries}")
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('info'),

            Stat::make('Admissions This Month', $admissionsThisMonth)
                ->description("Active Students: {$activeStudents}")
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),

            Stat::make('Today\'s Fee Collection', '₹' . number_format($todaysCollection, 2))
                ->description("Monthly: ₹" . number_format($monthlyCollection, 2))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Total Due Fees', '₹' . number_format($dueFees, 2))
                ->description("Upcoming (7d): {$upcomingInstallments}")
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color('danger'),

            Stat::make('Avg Attendance', "{$avgAttendance}%")
                ->description('Active student average')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color($avgAttendance >= 75 ? 'success' : 'warning'),
        ];
    }
}
