<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Certificate - {{ $certificate->certificate_no }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }
        body {
            font-family: 'Georgia', 'Times New Roman', Times, serif;
            background-color: #fcfbf7;
            color: #1e293b;
            margin: 0;
            padding: 0;
            height: 100vh;
        }
        .cert-border {
            position: absolute;
            top: 20px;
            left: 20px;
            right: 20px;
            bottom: 20px;
            border: 4px solid #1e3a8a;
            padding: 10px;
        }
        .cert-inner-border {
            border: 2px solid #b45309;
            height: 100%;
            box-sizing: border-box;
            padding: 40px;
            text-align: center;
        }
        .header {
            margin-top: 10px;
        }
        .institution {
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            color: #b45309;
            font-weight: bold;
            margin: 0 0 10px 0;
        }
        .title {
            font-size: 38px;
            font-weight: bold;
            color: #1e3a8a;
            margin: 10px 0;
            letter-spacing: 0.05em;
        }
        .sub-title {
            font-size: 14px;
            font-style: italic;
            color: #64748b;
            margin-bottom: 30px;
        }
        .certify-text {
            font-size: 16px;
            color: #334155;
            margin: 15px 0;
        }
        .student-name {
            font-family: 'Times New Roman', Times, serif;
            font-size: 32px;
            font-weight: bold;
            color: #0f172a;
            border-bottom: 2px solid #cbd5e1;
            display: inline-block;
            padding-bottom: 5px;
            min-width: 300px;
            margin: 10px 0;
        }
        .course-text {
            font-size: 16px;
            color: #334155;
            margin: 15px 0;
        }
        .course-name {
            font-size: 24px;
            font-weight: bold;
            color: #1e3a8a;
            margin: 10px 0;
        }
        .details-text {
            font-size: 13px;
            color: #64748b;
            margin-top: 15px;
        }
        .footer-section {
            position: absolute;
            bottom: 60px;
            left: 80px;
            right: 80px;
        }
        .signature-col {
            width: 30%;
            float: left;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #94a3b8;
            margin-top: 40px;
            padding-top: 5px;
            font-size: 13px;
            font-weight: bold;
            color: #334155;
        }
        .signature-title {
            font-size: 11px;
            color: #64748b;
        }
        .qr-col {
            width: 40%;
            float: left;
            text-align: center;
        }
        .qr-code {
            display: inline-block;
            margin-top: -10px;
        }
        .cert-number {
            position: absolute;
            bottom: 40px;
            left: 40px;
            font-size: 10px;
            color: #94a3b8;
            font-family: monospace;
        }
        .verify-text {
            margin-top: 2px;
            font-size: 9px;
            color: #94a3b8;
        }
        .clear {
            clear: both;
        }
    </style>
</head>
<body>
    <div class="cert-border">
        <div class="cert-inner-border">
            <!-- Header -->
            <div class="header">
                <p class="institution">{{ $setting?->company_name ?? 'EDU INSTITUTE OF TECHNOLOGY' }}</p>
                <h1 class="title">CERTIFICATE OF COMPLETION</h1>
                <p class="sub-title">Outstanding Academic Excellence & Training Achievement</p>
            </div>

            <!-- Certify Text -->
            <p class="certify-text">This is proudly presented to</p>
            <div class="student-name">{{ $certificate->admission->student_name }}</div>
            
            <p class="course-text">for successfully completing the specialized course of study in</p>
            <div class="course-name">{{ $certificate->course->course_name }}</div>
            
            <p class="details-text">
                Duration of Course: <strong>{{ $certificate->course->duration_months }} Months</strong> &bull; 
                Completion Date: <strong>{{ $certificate->completion_date->format('F d, Y') }}</strong>
            </p>

            <!-- Footer Signatures & QR Code -->
            <div class="footer-section">
                <!-- Instructor Signature -->
                <div class="signature-col">
                    <div class="signature-line">
                        {{ $certificate->admission->instructor ? $certificate->admission->instructor->name : 'Course Instructor' }}
                    </div>
                    <div class="signature-title">Course Instructor</div>
                </div>

                <!-- QR Code Verification -->
                <div class="qr-col">
                    <div class="qr-code">
                        <img src="{{ $qrCode }}" alt="Verification QR Code" width="80" height="80">
                        <div class="verify-text">Scan to Verify Authenticity</div>
                    </div>
                </div>

                <!-- Director Signature -->
                <div class="signature-col" style="float: right;">
                    <div class="signature-line">Director of Academics</div>
                    <div class="signature-title">{{ $setting?->company_name ?? 'EDU Institute' }} Authority</div>
                </div>
                
                <div class="clear"></div>
            </div>

            <!-- Certificate No -->
            <div class="cert-number">
                ID: {{ $certificate->certificate_no }} &bull; Verify: {{ url('/verify-certificate/' . $certificate->verification_token) }}
            </div>
        </div>
    </div>
</body>
</html>
