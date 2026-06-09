<?php

namespace App\Filament\Admin\Resources\FeePaymentResource\Pages;

use App\Filament\Admin\Resources\FeePaymentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFeePayment extends CreateRecord
{
    protected static string $resource = FeePaymentResource::class;

    protected function getRedirectUrl(): string
    {
        // Redirect back to the student profile if created from student profile
        $admissionId = request()->query('admission_id');
        if ($admissionId) {
            return route('filament.admin.resources.admissions.view', ['record' => $admissionId]);
        }
        return $this->getResource()::getUrl('index');
    }
}
