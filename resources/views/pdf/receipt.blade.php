<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Fee Receipt - {{ $payment->receipt_no }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            font-size: 14px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .logo-section {
            float: left;
        }
        .logo-title {
            font-size: 24px;
            font-weight: bold;
            color: #1e3a8a;
            margin: 0;
        }
        .logo-sub {
            font-size: 12px;
            color: #64748b;
            margin: 0;
        }
        .invoice-details {
            float: right;
            text-align: right;
        }
        .invoice-title {
            font-size: 20px;
            font-weight: bold;
            color: #0f172a;
            margin: 0;
        }
        .invoice-no {
            font-size: 14px;
            color: #f59e0b;
            font-weight: bold;
            margin: 5px 0 0 0;
        }
        .clear {
            clear: both;
        }
        .info-grid {
            margin-bottom: 30px;
            margin-top: 20px;
        }
        .info-col {
            width: 48%;
            float: left;
        }
        .info-col-right {
            width: 48%;
            float: right;
        }
        .section-title {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 5px;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .info-row {
            margin-bottom: 6px;
        }
        .info-label {
            font-weight: bold;
            color: #475569;
            display: inline-block;
            width: 120px;
        }
        .info-val {
            color: #0f172a;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th {
            background-color: #1e3a8a;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
            padding: 12px;
            font-size: 12px;
            text-transform: uppercase;
        }
        td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
            color: #334155;
        }
        .totals-section {
            float: right;
            width: 40%;
            margin-top: 10px;
        }
        .totals-row {
            padding: 8px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .totals-label {
            float: left;
            color: #64748b;
            font-weight: bold;
        }
        .totals-val {
            float: right;
            text-align: right;
            font-weight: bold;
            color: #0f172a;
        }
        .totals-grand {
            border-top: 2px solid #e2e8f0;
            padding-top: 10px;
            font-size: 16px;
        }
        .totals-grand .totals-val {
            color: #1e3a8a;
        }
        .footer {
            margin-top: 100px;
            border-top: 1px dashed #cbd5e1;
            padding-top: 15px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
        }
        .signature-area {
            float: left;
            width: 40%;
            margin-top: 40px;
            border-top: 1px solid #94a3b8;
            text-align: center;
            padding-top: 5px;
            font-size: 12px;
            color: #475569;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo-section">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" alt="Logo" style="max-height: 40px; margin-bottom: 5px; display: block;">
                @else
                    <h1 class="logo-title">{{ $setting?->company_name ?? 'EDU INSTITUTE' }}</h1>
                @endif
                <p class="logo-sub">{{ $setting?->description ?? 'Empowering Minds, Shaping Futures' }}</p>
            </div>
            <div class="invoice-details">
                <h2 class="invoice-title">PAYMENT RECEIPT</h2>
                <p class="invoice-no">{{ $payment->receipt_no }}</p>
            </div>
            <div class="clear"></div>
        </div>

        <!-- Info Grid -->
        <div class="info-grid">
            <div class="info-col">
                <div class="section-title">Student Information</div>
                <div class="info-row">
                    <span class="info-label">Name:</span>
                    <span class="info-val">{{ $payment->admission->student_name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Admission Number:</span>
                    <span class="info-val">{{ $payment->admission->admission_no }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Mobile:</span>
                    <span class="info-val">{{ $payment->admission->mobile }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email:</span>
                    <span class="info-val">{{ $payment->admission->email ?: 'N/A' }}</span>
                </div>
            </div>
            <div class="info-col-right">
                <div class="section-title">Receipt Information</div>
                <div class="info-row">
                    <span class="info-label">Date:</span>
                    <span class="info-val">{{ $payment->receipt_date->format('Y-m-d') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Payment Method:</span>
                    <span class="info-val">{{ $payment->payment_method }}</span>
                </div>
                @if($payment->transaction_reference)
                    <div class="info-row">
                        <span class="info-label">Txn Ref:</span>
                        <span class="info-val">{{ $payment->transaction_reference }}</span>
                    </div>
                @endif
            </div>
            <div class="clear"></div>
        </div>

        <!-- Payment Particulars -->
        <div class="section-title">Fee Particulars</div>
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align: right;">Total Amount</th>
                    <th style="text-align: right;">Amount Paid</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        Course Fee - {{ $payment->admission->course->course_name }}
                    </td>
                    <td style="text-align: right;">Rs.{{ number_format($payment->admission->final_fee, 2) }}</td>
                    <td style="text-align: right; font-weight: bold; color: #16a34a;">Rs.{{ number_format($payment->amount_paid, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Summary Totals -->
        <div>
            <div class="signature-area">
                Authorized Signature
            </div>
            <div class="totals-section">
                <div class="totals-row">
                    <span class="totals-label">Subtotal</span>
                    <span class="totals-val">Rs.{{ number_format($payment->amount_paid, 2) }}</span>
                    <div class="clear"></div>
                </div>
                <div class="totals-row totals-grand">
                    <span class="totals-label">Total Received</span>
                    <span class="totals-val">Rs.{{ number_format($payment->amount_paid, 2) }}</span>
                    <div class="clear"></div>
                </div>
                @if($payment->admission)
                    <div class="totals-row" style="margin-top: 10px; border-top: 1px solid #cbd5e1;">
                        <span class="totals-label" style="font-size: 11px;">Outstanding Balance</span>
                        <span class="totals-val" style="font-size: 11px; color: #dc2626;">
                            @php
                                $totalPaid = $payment->admission->payments()->sum('amount_paid');
                                $outstanding = max(0.00, $payment->admission->final_fee - $totalPaid);
                            @endphp
                            Rs.{{ number_format($outstanding, 2) }}
                        </span>
                        <div class="clear"></div>
                    </div>
                @endif
            </div>
            <div class="clear"></div>
        </div>

        <!-- Footer -->
        <div class="footer">
            Thank you for your payment! If you have any questions, please contact our accounts department.<br>
            <strong>{{ $setting?->company_name ?? 'EDU INSTITUTE' }}</strong>
            @if($setting?->address) &bull; {{ $setting->address }} @endif
            @if($setting?->mobile_no) &bull; {{ $setting->mobile_no }} @endif
            @if($setting?->support_email) &bull; {{ $setting->support_email }} @endif
            @if($setting?->website) &bull; {{ $setting->website }} @endif
        </div>
    </div>
</body>
</html>
