<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Enquiry;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class EnquirySourcesChart extends ChartWidget
{
    protected ?string $heading = 'Enquiry Sources';
    protected static ?int $sort = 2;

    public function getType(): string
    {
        return 'pie';
    }

    protected function getData(): array
    {
        $data = Enquiry::select('enquiry_source', DB::raw('count(*) as total'))
            ->groupBy('enquiry_source')
            ->pluck('total', 'enquiry_source')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Enquiries',
                    'data' => array_values($data),
                    'backgroundColor' => [
                        '#1e3a8a', '#b45309', '#10b981', '#ef4444', '#8b5cf6', '#3b82f6'
                    ],
                ],
            ],
            'labels' => array_keys($data),
        ];
    }
}
