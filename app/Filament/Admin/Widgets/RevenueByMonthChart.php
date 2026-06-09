<?php

namespace App\Filament\Admin\Widgets;

use App\Models\FeePayment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class RevenueByMonthChart extends ChartWidget
{
    protected ?string $heading = 'Revenue Collection (Current Year)';
    protected static ?int $sort = 4;

    public function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $revenue = FeePayment::select(
            DB::raw('MONTH(receipt_date) as month'),
            DB::raw('sum(amount_paid) as total')
        )
        ->whereYear('receipt_date', now()->year)
        ->groupBy('month')
        ->pluck('total', 'month')
        ->toArray();

        $data = [];
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        for ($i = 1; $i <= 12; $i++) {
            $data[] = floatval($revenue[$i] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Collection (₹)',
                    'data' => $data,
                    'fill' => true,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'borderColor' => '#10b981',
                    'tension' => 0.3,
                ],
            ],
            'labels' => $months,
        ];
    }
}
