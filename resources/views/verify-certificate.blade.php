<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Verification - {{ $setting?->company_name ?? 'EDU Institute' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
        .serif-font {
            font-family: 'Playfair Display', serif;
        }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex items-center justify-center p-4 selection:bg-amber-500 selection:text-slate-950">
    <div class="relative w-full max-w-2xl">
        <!-- Glow effects -->
        <div class="absolute -top-12 -left-12 w-48 h-48 bg-blue-500 rounded-full blur-3xl opacity-20 pointer-events-none"></div>
        <div class="absolute -bottom-12 -right-12 w-48 h-48 bg-amber-500 rounded-full blur-3xl opacity-20 pointer-events-none"></div>

        <div class="bg-slate-950 border border-slate-800 rounded-3xl p-8 sm:p-12 shadow-2xl relative overflow-hidden backdrop-blur-md">
            @if($certificate)
                <!-- Header Icon (Success) -->
                <div class="flex justify-center mb-8">
                    <div class="w-20 h-20 bg-emerald-500/10 border border-emerald-500/30 rounded-full flex items-center justify-center text-emerald-400 shadow-lg shadow-emerald-500/5">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="24" stroke-dashoffset="0" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                </div>

                <div class="text-center space-y-2">
                    <span class="text-xs font-semibold tracking-widest text-emerald-400 uppercase bg-emerald-500/10 border border-emerald-500/20 px-3 py-1 rounded-full">
                        Official Record Verified
                    </span>
                    <h1 class="text-3xl font-extrabold text-white tracking-tight sm:text-4xl mt-2">
                        Authentic Certificate
                    </h1>
                    <p class="text-slate-400 text-sm max-w-md mx-auto mt-2">
                        This digital certificate matches our official academic record registry database.
                    </p>
                </div>

                <!-- Record Details Card -->
                <div class="mt-8 bg-slate-900/60 border border-slate-800/80 rounded-2xl p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-slate-500 block text-xs uppercase font-semibold tracking-wider">Student Name</span>
                            <span class="text-white font-bold text-lg block serif-font">{{ $certificate->admission->student_name }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block text-xs uppercase font-semibold tracking-wider">Certificate ID</span>
                            <span class="text-slate-300 font-mono font-semibold block">{{ $certificate->certificate_no }}</span>
                        </div>
                    </div>

                    <div class="border-t border-slate-800/60 pt-4">
                        <span class="text-slate-500 block text-xs uppercase font-semibold tracking-wider">Course of Study</span>
                        <span class="text-amber-400 font-bold text-xl block serif-font">{{ $certificate->course->course_name }}</span>
                    </div>

                    <div class="grid grid-cols-2 gap-4 border-t border-slate-800/60 pt-4 text-sm">
                        <div>
                            <span class="text-slate-500 block text-xs uppercase font-semibold tracking-wider">Completion Date</span>
                            <span class="text-slate-200 font-semibold block">{{ $certificate->completion_date->format('F d, Y') }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block text-xs uppercase font-semibold tracking-wider">Date of Issue</span>
                            <span class="text-slate-200 font-semibold block">{{ $certificate->issue_date->format('F d, Y') }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-8 text-center text-xs text-slate-500">
                    {{ $setting?->company_name ?? 'EDU Institute' }} authority verifies that the recipient named above has met all required academic and practice guidelines.
                </div>
            @else
                <!-- Header Icon (Failed) -->
                <div class="flex justify-center mb-8">
                    <div class="w-20 h-20 bg-rose-500/10 border border-rose-500/30 rounded-full flex items-center justify-center text-rose-400 shadow-lg shadow-rose-500/5">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                </div>

                <div class="text-center space-y-2">
                    <span class="text-xs font-semibold tracking-widest text-rose-400 uppercase bg-rose-500/10 border border-rose-500/20 px-3 py-1 rounded-full">
                        Verification Failed
                    </span>
                    <h1 class="text-3xl font-extrabold text-white tracking-tight sm:text-4xl mt-2">
                        Invalid Certificate
                    </h1>
                    <p class="text-slate-400 text-sm max-w-md mx-auto mt-2">
                        The verification token is invalid, expired, or has been revoked from our registry database.
                    </p>
                </div>

                <div class="mt-8 p-6 bg-rose-500/5 border border-rose-500/10 rounded-2xl text-center text-sm text-slate-300">
                    Please make sure the URL was not altered. For verification inquiries, contact the administrative office at 
                    <a href="mailto:{{ $setting?->support_email ?? 'support@eduinstitute.com' }}" class="text-amber-400 font-semibold hover:underline block mt-1">{{ $setting?->support_email ?? 'support@eduinstitute.com' }}</a>
                </div>
            @endif

            <!-- Bottom Brand -->
            <div class="mt-12 border-t border-slate-800/80 pt-6 text-center text-slate-500 text-sm">
                &copy; {{ date('Y') }} {{ strtoupper($setting?->company_name ?? 'EDU INSTITUTE') }}. All rights reserved.
            </div>
        </div>
    </div>
</body>
</html>
