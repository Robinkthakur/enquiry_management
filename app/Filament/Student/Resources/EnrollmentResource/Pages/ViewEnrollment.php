<?php

namespace App\Filament\Student\Resources\EnrollmentResource\Pages;

use App\Filament\Student\Resources\EnrollmentResource;
use Filament\Resources\Pages\ViewRecord;

class ViewEnrollment extends ViewRecord
{
    protected static string $resource = EnrollmentResource::class;

    protected string $view = 'filament.student.resources.enrollment.view';
}
