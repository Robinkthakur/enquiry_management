@php
    $record = $this->getRecord();
    $course = $record->course;
    $instructor = $record->instructor;
    $installments = $record->installments()->orderBy('installment_no')->get();
    $payments = $record->payments()->orderBy('receipt_date', 'desc')->get();
    
    $totalPaid = $payments->where('status', 'Verified')->sum('amount_paid');
    $totalPending = $payments->where('status', 'Pending')->sum('amount_paid');
    $totalDue = max(0.00, $record->final_fee - $totalPaid);
    
    $statusColor = match ($record->status) {
        'Active' => 'success',
        'Hold' => 'warning',
        'Completed' => 'info',
        'Cancelled' => 'danger',
        default => 'gray',
    };
@endphp

<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Top Row: Course Overview and Schedule -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Course Details Card (Left 2/3) -->
            <div class="lg:col-span-2 space-y-6">
                <div class="p-6 bg-white border border-gray-100 rounded-xl shadow-sm dark:bg-gray-900 dark:border-gray-800">
                    <div class="flex items-start justify-between border-b border-gray-100 dark:border-gray-800 pb-4 mb-4">
                        <div>
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-md bg-primary-50 text-primary-700 dark:bg-primary-500/10 dark:text-primary-400">
                                {{ $course->course_code }}
                            </span>
                            <h2 class="mt-2 text-2xl font-extrabold text-gray-900 dark:text-white">{{ $course->course_name }}</h2>
                        </div>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800 dark:bg-{{ $statusColor }}-500/10 dark:text-{{ $statusColor }}-400">
                            {{ $record->status }}
                        </span>
                    </div>

                    <div class="prose dark:prose-invert max-w-none text-sm text-gray-600 dark:text-gray-300">
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-2">Course Description</h3>
                        <p class="leading-relaxed">
                            {{ $course->description ?: 'No description available for this course.' }}
                        </p>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-3 gap-6 mt-6 pt-6 border-t border-gray-100 dark:border-gray-800">
                        <div>
                            <span class="text-xs text-gray-400 font-medium block">Duration</span>
                            <span class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{{ $course->duration_months }} Months</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-400 font-medium block">Course Standard Fee</span>
                            <span class="text-sm text-gray-800 dark:text-gray-200 font-semibold">₹{{ number_format($course->total_fee, 2) }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-400 font-medium block">Registration Fee</span>
                            <span class="text-sm text-gray-800 dark:text-gray-200 font-semibold">₹{{ number_format($course->registration_fee, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enrollment Details & Instructor Card (Right 1/3) -->
            <div class="lg:col-span-1 space-y-6">
                <div class="p-6 bg-white border border-gray-100 rounded-xl shadow-sm dark:bg-gray-900 dark:border-gray-800">
                    <h3 class="text-md font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3 mb-4">
                        Schedule & Instructor
                    </h3>

                    <div class="space-y-4">
                        <div>
                            <span class="text-xs text-gray-400 font-medium block mb-1">Time Slot</span>
                            <div class="flex items-center gap-2 text-sm font-semibold text-gray-800 dark:text-gray-200">
                                <svg class="w-4.5 h-4.5 text-primary-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                </svg>
                                {{ $record->time_slot ?: 'Not Assigned yet' }}
                            </div>
                        </div>

                        <div class="pt-3 border-t border-gray-50 dark:border-gray-800">
                            <span class="text-xs text-gray-400 font-medium block mb-2">Assigned Instructor</span>
                            @if($instructor)
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-primary-500 to-indigo-500 flex items-center justify-center text-white font-bold text-sm">
                                        {{ strtoupper(substr($instructor->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-900 dark:text-white">{{ $instructor->name }}</h4>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $instructor->email }}</p>
                                    </div>
                                </div>
                            @else
                                <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg text-xs text-gray-500 dark:text-gray-400 flex items-center gap-2">
                                    <svg class="w-4.5 h-4.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
                                    </svg>
                                    Not Assigned yet.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Row: Fees, Installments and Receipts -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Fee Installments (Left 2/3) -->
            <div class="lg:col-span-2 space-y-6">
                <div class="p-6 bg-white border border-gray-100 rounded-xl shadow-sm dark:bg-gray-900 dark:border-gray-800">
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3 mb-4">
                        <h3 class="text-md font-bold text-gray-900 dark:text-white">Fee Installments Breakdown</h3>
                        <span class="text-xs text-gray-400 font-medium">Final Enrolled Fee: <strong class="text-gray-800 dark:text-white">₹{{ number_format($record->final_fee, 2) }}</strong></span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-800 dark:text-gray-400">
                                <tr>
                                    <th class="px-4 py-3">No</th>
                                    <th class="px-4 py-3">Due Date</th>
                                    <th class="px-4 py-3 text-right">Amount</th>
                                    <th class="px-4 py-3 text-right">Paid</th>
                                    <th class="px-4 py-3 text-right">Due</th>
                                    <th class="px-4 py-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @forelse($installments as $inst)
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30">
                                        <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">#{{ $inst->installment_no }}</td>
                                        <td class="px-4 py-3">{{ $inst->due_date ? \Carbon\Carbon::parse($inst->due_date)->format('M d, Y') : 'N/A' }}</td>
                                        <td class="px-4 py-3 text-right font-medium text-gray-900 dark:text-white">₹{{ number_format($inst->amount, 2) }}</td>
                                        <td class="px-4 py-3 text-right text-success-600 dark:text-success-400">₹{{ number_format($inst->paid_amount, 2) }}</td>
                                        <td class="px-4 py-3 text-right text-danger-600 dark:text-danger-400">₹{{ number_format($inst->due_amount, 2) }}</td>
                                        <td class="px-4 py-3 text-center">
                                            @php
                                                $instColor = match ($inst->status) {
                                                    'Paid' => 'success',
                                                    'Partial' => 'warning',
                                                    'Pending' => 'danger',
                                                    default => 'gray',
                                                };
                                            @endphp
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-{{ $instColor }}-100 text-{{ $instColor }}-800 dark:bg-{{ $instColor }}-500/10 dark:text-{{ $instColor }}-400">
                                                {{ $inst->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-4 text-center text-gray-400">No installments defined.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Financial Summary & Recent Payments (Right 1/3) -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Mini Stats Panel -->
                <div class="p-6 bg-white border border-gray-100 rounded-xl shadow-sm dark:bg-gray-900 dark:border-gray-800 space-y-4">
                    <h3 class="text-md font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3">
                        Payments Overview
                    </h3>
                    
                    <div class="grid grid-cols-1 gap-3">
                        <div class="p-3 bg-success-50 dark:bg-success-950/20 border border-success-100 dark:border-success-900/30 rounded-lg">
                            <span class="text-xs text-success-700 dark:text-success-400 font-semibold block">Total Paid (Verified)</span>
                            <span class="text-lg font-bold text-success-800 dark:text-success-300">₹{{ number_format($totalPaid, 2) }}</span>
                        </div>
                        
                        @if($totalPending > 0)
                            <div class="p-3 bg-warning-50 dark:bg-warning-950/20 border border-warning-100 dark:border-warning-900/30 rounded-lg">
                                <span class="text-xs text-warning-700 dark:text-warning-400 font-semibold block">Pending Verification</span>
                                <span class="text-lg font-bold text-warning-800 dark:text-warning-300">₹{{ number_format($totalPending, 2) }}</span>
                            </div>
                        @endif

                        <div class="p-3 bg-danger-50 dark:bg-danger-950/20 border border-danger-100 dark:border-danger-900/30 rounded-lg">
                            <span class="text-xs text-danger-700 dark:text-danger-400 font-semibold block">Remaining Balance</span>
                            <span class="text-lg font-bold text-danger-800 dark:text-danger-300">₹{{ number_format($totalDue, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Recent Payments History -->
                <div class="p-6 bg-white border border-gray-100 rounded-xl shadow-sm dark:bg-gray-900 dark:border-gray-800">
                    <h3 class="text-md font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3 mb-4">
                        Payments & Receipts
                    </h3>

                    <div class="space-y-3 max-h-64 overflow-y-auto">
                        @forelse($payments as $pmt)
                            <div class="p-3 bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-700 rounded-xl flex items-center justify-between text-xs">
                                <div>
                                    <div class="font-bold text-gray-900 dark:text-white">{{ $pmt->receipt_no ?: 'REF: '.$pmt->transaction_reference }}</div>
                                    <div class="text-gray-400 mt-0.5">{{ \Carbon\Carbon::parse($pmt->receipt_date)->format('Y-m-d') }} &bull; {{ $pmt->payment_method }}</div>
                                    
                                    @php
                                        $pmtStatusColor = match ($pmt->status) {
                                            'Verified' => 'success',
                                            'Pending' => 'warning',
                                            'Rejected' => 'danger',
                                            default => 'gray',
                                        };
                                    @endphp
                                    <span class="mt-1 inline-block px-1.5 py-0.2 font-semibold rounded bg-{{ $pmtStatusColor }}-150 text-{{ $pmtStatusColor }}-800 text-[10px]">
                                        {{ $pmt->status }}
                                    </span>
                                </div>
                                <div class="text-right">
                                    <span class="font-bold text-gray-800 dark:text-gray-200 block text-sm">₹{{ number_format($pmt->amount_paid, 2) }}</span>
                                    @if($pmt->status === 'Verified')
                                        <a href="{{ route('admin.payments.receipt', ['payment' => $pmt->id]) }}" target="_blank" class="text-primary-600 hover:underline inline-block mt-1 font-semibold">
                                            Download Receipt
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-gray-400 text-center py-4">No payments recorded for this enrollment.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
