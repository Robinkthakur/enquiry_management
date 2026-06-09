<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Admission;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class AdmissionsByMonthChart extends ChartWidget
{
    protected ?string $heading = 'Admissions By Month (Current Year)';
    protected static ?int $sort = 3;

    public function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $admissions = Admission::select(
            DB::raw('MONTH(admission_date) as month'),
            DB::raw('count(*) as total')
        )
        ->whereYear('admission_date', now()->year)
        ->groupBy('month')
        ->pluck('total', 'month')
        ->toArray();

        $data = [];
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        for ($i = 1; $i <= 12; $i++) {
            $data[] = $admissions[$i] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Students Admitted',
                    'data' => $data,
                    'backgroundColor' => '#3b82f6',
                    'borderColor' => '#1d4ed8',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $months,
        ];
    }
}
