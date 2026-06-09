<x-filament-panels::page>
    <form wire:submit.prevent="save" class="space-y-6">
        <!-- Form Fields (Batch & Date) -->
        <div class="p-6 bg-white border border-gray-100 rounded-xl shadow-sm dark:bg-gray-900 dark:border-gray-800">
            {{ $this->form }}
        </div>

        <!-- Student Attendance List -->
        @if($this->course_id && $this->time_slot)
            <div class="bg-white border border-gray-100 rounded-xl shadow-sm dark:bg-gray-900 dark:border-gray-800 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 dark:bg-gray-900 dark:border-gray-800 flex items-center justify-between">
                    <h3 class="text-md font-bold text-gray-900 dark:text-white">Student Checklist</h3>
                    <span class="text-xs font-semibold px-2 py-1 bg-primary-100 text-primary-800 dark:bg-primary-500/10 dark:text-primary-400 rounded-full">
                        {{ $this->students->count() }} Students Active
                    </span>
                </div>
                
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($this->students as $student)
                        <div class="p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                            <!-- Student Info -->
                            <div class="flex items-center space-x-3">
                                @if($student->student_photo)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('local')->url($student->student_photo) }}" alt="Student photo" class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-primary-500 flex items-center justify-center text-white text-xs font-bold">
                                        {{ strtoupper(substr($student->student_name, 0, 2)) }}
                                    </div>
                                @endif
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $student->student_name }}</h4>
                                    <span class="text-xs text-gray-400 font-medium">{{ $student->roll_no }}</span>
                                </div>
                            </div>

                            <!-- Attendance Toggle Buttons (Buttons styled) -->
                            <div class="flex items-center space-x-2">
                                <!-- Present Button -->
                                <button type="button" 
                                        wire:click="setStatus('{{ $student->id }}', 'Present')" 
                                        class="px-4 py-2 text-xs font-semibold rounded-lg border transition-all duration-75 {{ ($attendance_statuses[$student->id] ?? '') === 'Present' ? 'border-success-500 bg-success-50 text-success-800 dark:bg-success-500/10 dark:text-success-400' : 'text-gray-600 bg-white hover:bg-gray-50 border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700' }}">
                                    Present
                                </button>

                                <!-- Absent Button -->
                                <button type="button" 
                                        wire:click="setStatus('{{ $student->id }}', 'Absent')" 
                                        class="px-4 py-2 text-xs font-semibold rounded-lg border transition-all duration-75 {{ ($attendance_statuses[$student->id] ?? '') === 'Absent' ? 'border-danger-500 bg-danger-50 text-danger-800 dark:bg-danger-500/10 dark:text-danger-400' : 'text-gray-600 bg-white hover:bg-gray-50 border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700' }}">
                                    Absent
                                </button>

                                <!-- Leave Button -->
                                <button type="button" 
                                        wire:click="setStatus('{{ $student->id }}', 'Leave')" 
                                        class="px-4 py-2 text-xs font-semibold rounded-lg border transition-all duration-75 {{ ($attendance_statuses[$student->id] ?? '') === 'Leave' ? 'border-warning-500 bg-warning-50 text-warning-800 dark:bg-warning-500/10 dark:text-warning-400' : 'text-gray-600 bg-white hover:bg-gray-50 border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700' }}">
                                    Leave
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-400 text-sm">
                            No active students found for this course and time slot.
                        </div>
                    @endforelse
                </div>

                @if($this->students->isNotEmpty())
                    <div class="p-6 bg-gray-50 dark:bg-gray-900 border-t border-gray-100 dark:border-gray-800 flex justify-end">
                        <button type="submit" 
                                class="fi-btn fi-btn-size-md relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-color-primary bg-primary-600 hover:bg-primary-500 text-white shadow-sm py-2 px-6 inline-flex">
                            Save Attendance
                        </button>
                    </div>
                @endif
            </div>
        @else
            <div class="p-12 text-center border border-dashed border-gray-200 dark:border-gray-800 rounded-xl bg-white dark:bg-gray-900">
                <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-700 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">No Course/Time Slot Selected</h3>
                <p class="text-xs text-gray-400 mt-1">Select a course and time slot above to load the student checklist.</p>
            </div>
        @endif
    </form>
</x-filament-panels::page>
