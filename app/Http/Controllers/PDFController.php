<?php

namespace App\Http\Controllers;

use App\Models\FeePayment;
use App\Models\Certificate;
use App\Models\CompanySetting;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Http\Response;

class PDFController extends Controller
{
    /**
     * Generate and stream fee payment receipt PDF.
     */
    public function downloadReceipt(FeePayment $payment)
    {
        $payment->load(['admission.course', 'installment']);
        
        $setting = CompanySetting::first();
        $logoBase64 = null;
        if ($setting && $setting->logo) {
            $logoPath = storage_path('app/public/' . $setting->logo);
            if (file_exists($logoPath)) {
                $logoBase64 = 'data:image/' . pathinfo($logoPath, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($logoPath));
            }
        }
        
        $pdf = Pdf::loadView('pdf.receipt', compact('payment', 'setting', 'logoBase64'));
        
        return $pdf->stream($payment->receipt_no . '.pdf');
    }

    /**
     * Generate and stream landscape completion certificate PDF.
     */
    public function downloadCertificate(Certificate $certificate)
    {
        $certificate->load(['admission.course']);

        // Generate base64 QR Code pointing to verification URL
        $verifyUrl = url('/verify-certificate/' . $certificate->verification_token);
        
        // SimpleQRCode generates SVG to avoid dependency on Imagick extension
        $qrCodeImage = QrCode::format('svg')
            ->size(120)
            ->margin(1)
            ->generate($verifyUrl);
            
        $qrCode = 'data:image/svg+xml;base64,' . base64_encode($qrCodeImage);

        $setting = CompanySetting::first();

        $pdf = Pdf::loadView('pdf.certificate', compact('certificate', 'qrCode', 'setting'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream($certificate->certificate_no . '.pdf');
    }
}
