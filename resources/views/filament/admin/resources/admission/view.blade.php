@php
    $record = $this->getRecord();
    $totalPaid = $record->payments()->sum('amount_paid');
    $totalDue = max(0.00, $record->final_fee - $totalPaid);
    $attendancePct = $record->attendance_percentage;
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
      

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Profile Card (Left) -->
            <div class="lg:col-span-1 space-y-6">
                <div class="p-6 bg-white border border-gray-100 rounded-xl shadow-sm dark:bg-gray-900 dark:border-gray-800 flex flex-col items-center text-center">
                    <!-- Photo -->
                    @if($record->student_photo)
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('local')->url($record->student_photo) }}" alt="Student photo" class="w-32 h-32 rounded-xl object-cover shadow-md border-4 border-primary-500/20">
                    @else
                        <div class="w-32 h-32 rounded-xl bg-gradient-to-tr from-primary-500 to-amber-300 flex items-center justify-center text-white text-4xl font-bold shadow-md border-4 border-primary-500/10">
                            {{ strtoupper(substr($record->student_name, 0, 2)) }}
                        </div>
                    @endif

                    <h2 class="mt-4 text-xl font-bold text-gray-900 dark:text-white">{{ $record->student_name }}</h2>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $record->roll_no }}</p>
                    
                    <span class="mt-2 px-2.5 py-0.5 text-xs font-semibold rounded-full bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800 dark:bg-{{ $statusColor }}-500/10 dark:text-{{ $statusColor }}-400">
                        {{ $record->status }}
                    </span>

                    <div class="w-full mt-6 border-t border-gray-100 dark:border-gray-800 pt-4 text-left space-y-3">
                        <div>
                            <span class="text-xs text-gray-400 font-medium block">Course</span>
                            <span class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{{ $record->course->course_name }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-400 font-medium block">Time Slot</span>
                            <span class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{{ $record->time_slot ?: 'Not Assigned' }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-400 font-medium block">Instructor</span>
                            <span class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{{ $record->instructor ? $record->instructor->name : 'Not Assigned' }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-400 font-medium block">Mobile</span>
                            <span class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{{ $record->mobile }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-400 font-medium block">Email</span>
                            <span class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{{ $record->email ?: 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-400 font-medium block">Address</span>
                            <span class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{{ $record->address ?: 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Dashboard Content (Right) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Metrics Panel -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Fee Collection Metric -->
                    <div class="p-6 bg-white border border-gray-100 rounded-xl shadow-sm dark:bg-gray-900 dark:border-gray-800">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm font-semibold text-gray-500 dark:text-gray-400">Fees Progress</span>
                            <span class="text-lg font-bold text-gray-800 dark:text-white">₹{{ number_format($totalPaid, 2) }} / ₹{{ number_format($record->final_fee, 2) }}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-3 dark:bg-gray-800">
                            @php $feePct = $record->final_fee > 0 ? ($totalPaid / $record->final_fee) * 100 : 0; @endphp
                            <div class="bg-success-600 h-3 rounded-full" style="width: {{ min(100, $feePct) }}%"></div>
                        </div>
                        <p class="mt-2 text-xs text-gray-400 font-medium">{{ number_format($feePct, 1) }}% paid (Remaining due: ₹{{ number_format($totalDue, 2) }})</p>
                    </div>

                    <!-- Attendance Metric -->
                    <div class="p-6 bg-white border border-gray-100 rounded-xl shadow-sm dark:bg-gray-900 dark:border-gray-800">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm font-semibold text-gray-500 dark:text-gray-400">Attendance Percentage</span>
                            <span class="text-lg font-bold text-gray-800 dark:text-white">{{ $attendancePct }}%</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-3 dark:bg-gray-800">
                            <div class="bg-primary-600 h-3 rounded-full" style="width: {{ $attendancePct }}%"></div>
                        </div>
                        <p class="mt-2 text-xs text-gray-400 font-medium">Goal: 75% or higher</p>
                    </div>
                </div>

                <!-- Custom Tabs Component -->
                <div x-data="{ activeTab: 'installments' }" class="bg-white border border-gray-100 rounded-xl shadow-sm dark:bg-gray-900 dark:border-gray-800 overflow-hidden">
                    <!-- Tab Headers -->
                    <div class="flex border-b border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-900">
                        <button @click="activeTab = 'installments'" :class="activeTab === 'installments' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-6 py-4 text-sm font-medium border-b-2 outline-none">
                            Payments & Receipts
                        </button>
                        <button @click="activeTab = 'attendance'" :class="activeTab === 'attendance' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-6 py-4 text-sm font-medium border-b-2 outline-none">
                            Attendance Logs
                        </button>
                        <button @click="activeTab = 'holds'" :class="activeTab === 'holds' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-6 py-4 text-sm font-medium border-b-2 outline-none">
                            Fee Holds
                        </button>
                        <button @click="activeTab = 'certificates'" :class="activeTab === 'certificates' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-6 py-4 text-sm font-medium border-b-2 outline-none">
                            Certificates
                        </button>
                    </div>

                    <!-- Tab Contents -->
                    <div class="p-6">
                        <!-- Installments Tab -->
                        <div x-show="activeTab === 'installments'" class="space-y-6">
                            <div>
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-md font-bold text-gray-900 dark:text-white">Fee Summary</h3>
                                    <a href="{{ route('filament.admin.resources.fee-payments.index') }}?tableFilters[admission_id][value]={{ $record->id }}" class="text-sm text-primary-600 hover:underline">
                                        View Payment History
                                    </a>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                    <div class="p-4 bg-gray-50 rounded-xl dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm">
                                        <span class="text-xs text-gray-400 font-medium block">Total Course Fee</span>
                                        <span class="text-lg font-bold text-gray-800 dark:text-white">₹{{ number_format($record->final_fee, 2) }}</span>
                                    </div>
                                    <div class="p-4 bg-gray-50 rounded-xl dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm">
                                        <span class="text-xs text-gray-400 font-medium block">Total Paid</span>
                                        <span class="text-lg font-bold text-success-600 dark:text-success-400">₹{{ number_format($totalPaid, 2) }}</span>
                                    </div>
                                    <div class="p-4 bg-gray-50 rounded-xl dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm flex justify-between items-center">
                                        <div>
                                            <span class="text-xs text-gray-400 font-medium block">Remaining Balance</span>
                                            <span class="text-lg font-bold text-danger-600 dark:text-danger-400">₹{{ number_format($totalDue, 2) }}</span>
                                        </div>
                                        @if($totalDue > 0)
                                            <a href="{{ route('filament.admin.resources.fee-payments.index') }}/create?admission_id={{ $record->id }}&amount_paid={{ $totalDue }}" class="px-3 py-1.5 text-xs font-semibold text-white bg-primary-600 hover:bg-primary-500 rounded-lg shadow transition-colors">
                                                Record Payment
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="border-t dark:border-gray-800 pt-6">
                                <h3 class="text-md font-bold text-gray-900 dark:text-white mb-4">Payment Receipts</h3>
                                <div class="space-y-3">
                                    @forelse($record->payments as $payment)
                                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl dark:bg-gray-800 border dark:border-gray-700">
                                            <div>
                                                <span class="font-bold text-sm text-gray-900 dark:text-white">{{ $payment->receipt_no }}</span>
                                                <span class="text-xs text-gray-400 block">{{ $payment->receipt_date->format('Y-m-d') }} &bull; {{ $payment->payment_method }}</span>
                                            </div>
                                            <div class="flex items-center space-x-3">
                                                <span class="text-sm font-bold text-success-600">₹{{ number_format($payment->amount_paid, 2) }}</span>
                                                <a href="/admin/payments/{{ $payment->id }}/receipt" target="_blank" class="text-xs text-primary-600 font-semibold hover:underline flex items-center gap-1">
                                                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                                                    Download PDF
                                                </a>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-sm text-gray-400">No payment receipts generated yet.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Attendance Tab -->
                        <div x-show="activeTab === 'attendance'">
                            <h3 class="text-md font-bold text-gray-900 dark:text-white mb-4">Attendance logs</h3>
                            <div class="overflow-y-auto max-h-96">
                                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-800 dark:text-gray-400">
                                        <tr>
                                            <th class="px-4 py-3">Date</th>
                                            <th class="px-4 py-3">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($record->attendances as $att)
                                            <tr class="border-b dark:border-gray-800">
                                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $att->attendance_date->format('Y-m-d') }}</td>
                                                <td class="px-4 py-3">
                                                    @php
                                                        $attColor = match($att->status) {
                                                            'Present' => 'success',
                                                            'Absent' => 'danger',
                                                            'Leave' => 'warning',
                                                            default => 'gray',
                                                        };
                                                    @endphp
                                                    <span class="px-2 py-1 text-xs font-semibold rounded bg-{{ $attColor }}-100 text-{{ $attColor }}-800 dark:bg-{{ $attColor }}-500/10 dark:text-{{ $attColor }}-400">
                                                        {{ $att->status }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="px-4 py-4 text-center text-gray-400">No attendance registered yet.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Holds Tab -->
                        <div x-show="activeTab === 'holds'" class="space-y-4">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-md font-bold text-gray-900 dark:text-white">Fee Reminder Holds</h3>
                                <a href="{{ route('filament.admin.resources.fee-holds.index') }}/create?admission_id={{ $record->id }}" class="text-sm font-semibold text-primary-600 hover:underline">
                                    + Add New Hold
                                </a>
                            </div>
                            <div class="space-y-3">
                                @forelse($record->holds as $hold)
                                    <div class="p-4 bg-gray-50 rounded-xl dark:bg-gray-800 border dark:border-gray-700 text-sm">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="font-bold text-gray-800 dark:text-gray-200">
                                                Hold: {{ $hold->hold_from->format('Y-m-d') }} to {{ $hold->hold_to ? $hold->hold_to->format('Y-m-d') : 'Indefinite' }}
                                            </span>
                                            <span class="text-xs text-gray-400">Approved by: {{ $hold->approver->name }}</span>
                                        </div>
                                        <p class="text-gray-600 dark:text-gray-400">Reason: {{ $hold->reason }}</p>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-400">No fee holds on this record.</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Certificates Tab -->
                        <div x-show="activeTab === 'certificates'" class="space-y-4">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-md font-bold text-gray-900 dark:text-white">Issued Certificates</h3>
                                @if($record->status === 'Completed')
                                    <a href="{{ route('filament.admin.resources.certificates.index') }}/create?admission_id={{ $record->id }}&course_id={{ $record->course_id }}" class="text-sm font-semibold text-primary-600 hover:underline">
                                        + Generate Certificate
                                    </a>
                                @endif
                            </div>
                            <div class="space-y-3">
                                @forelse($record->certificates as $cert)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl dark:bg-gray-800 border dark:border-gray-700">
                                        <div>
                                            <span class="font-bold text-sm text-gray-900 dark:text-white block">{{ $cert->certificate_no }}</span>
                                            <span class="text-xs text-gray-400 block">Issued on: {{ $cert->issue_date->format('Y-m-d') }}</span>
                                        </div>
                                        <div class="flex items-center space-x-3">
                                            <a href="/verify-certificate/{{ $cert->verification_token }}" target="_blank" class="text-xs text-success-600 font-semibold hover:underline">
                                                Verify Link
                                            </a>
                                            <a href="/admin/certificates/{{ $cert->id }}/pdf" target="_blank" class="text-xs text-primary-600 font-semibold hover:underline">
                                                Download PDF
                                            </a>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-400">No certificates generated for this student yet.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
