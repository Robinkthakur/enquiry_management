<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Course;
use Filament\Widgets\ChartWidget;

class CourseWiseStudentsChart extends ChartWidget
{
    protected ?string $heading = 'Active Students By Course';
    protected static ?int $sort = 5;

    public function getType(): string
    {
        return 'polarArea';
    }

    protected function getData(): array
    {
        $courses = Course::withCount([
            'admissions' => fn ($q) => $q->where('admission_courses.status', 'Active')
        ])
        ->get()
        ->pluck('admissions_count', 'course_code')
        ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Active Students',
                    'data' => array_values($courses),
                    'backgroundColor' => [
                        '#3b82f6', '#10b981', '#f59e0b', '#ec4899', '#8b5cf6'
                    ],
                ],
            ],
            'labels' => array_keys($courses),
        ];
    }
}
