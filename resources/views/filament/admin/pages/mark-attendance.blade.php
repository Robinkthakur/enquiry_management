<x-filament-panels::page>
    <form wire:submit.prevent="save" class="space-y-6">
        <!-- Form Fields (Date) -->
        <div class="p-6 bg-white border border-gray-100 rounded-xl shadow-sm dark:bg-gray-900 dark:border-gray-800">
            {{ $this->form }}
        </div>

        <!-- Student Attendance List -->
        @if($this->attendance_date)
            <div class="bg-white border border-gray-100 rounded-xl shadow-sm dark:bg-gray-900 dark:border-gray-800 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 dark:bg-gray-900 dark:border-gray-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <h3 class="text-md font-bold text-gray-900 dark:text-white">Student Checklist</h3>
                    <div class="relative w-full sm:w-72">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.602 10.602Z" />
                            </svg>
                        </div>
                        <input type="text" 
                               wire:model.live.debounce.300ms="search" 
                               placeholder="Search name or admission no..." 
                               class="block w-full rounded-lg border-0 py-1.5 pl-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 sm:text-sm sm:leading-6 dark:bg-gray-800 dark:text-white dark:ring-gray-700 dark:focus:ring-primary-500">
                    </div>
                </div>
                
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($this->students as $student)
                        <div class="p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                            <!-- Student Info -->
                             <div class="flex items-center space-x-3">
                                 @if($student->student_photo)
                                     <img src="{{ route('admin.student-photo', ['path' => $student->student_photo]) }}" alt="Student photo" class="w-10 h-10 rounded-full object-cover">
                                 @else
                                    <div class="w-10 h-10 rounded-full bg-primary-500 flex items-center justify-center text-white text-xs font-bold">
                                        {{ strtoupper(substr($student->student_name, 0, 2)) }}
                                    </div>
                                @endif
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $student->student_name }}</h4>
                                    <span class="text-xs text-gray-400 font-medium">{{ $student->admission_no }}</span>
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
                            No active students found.
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
        @endif
    </form>
</x-filament-panels::page>
