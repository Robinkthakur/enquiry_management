<x-filament-panels::page>
    @if(!$admission)
        <div class="p-6 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl dark:bg-amber-950/20 dark:border-amber-900/50 dark:text-amber-400">
            <h3 class="font-bold text-lg">Student Profile Not Linked</h3>
            <p class="mt-2 text-sm">Your user account is not linked to any student admission record. Please contact the administrator to link your profile.</p>
        </div>
    @else
        <div class="space-y-6">
            <!-- Header Section -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Welcome back, {{ auth()->user()->name }}! 👋</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Here's what's happening with your academics.</p>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Enrolled Courses -->
                <a href="/student/enrollments" class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm flex items-center space-x-4 hover:shadow-md hover:border-blue-200 dark:hover:border-blue-900/50 transition-all duration-200 block group">
                    <div class="flex items-center space-x-4 w-full">
                        <div class="p-4 rounded-xl bg-blue-50 dark:bg-blue-950/30 text-blue-600 dark:text-blue-400 group-hover:bg-blue-100 dark:group-hover:bg-blue-900/40 transition-colors duration-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/></svg>
                        </div>
                        <div>
                            <span class="text-xs text-gray-455 dark:text-gray-400 font-medium block">Enrolled Courses</span>
                            <span class="text-2xl font-bold text-gray-900 dark:text-white mt-1 block">{{ $enrolledCoursesCount }}</span>
                            <span class="text-xs text-blue-600 group-hover:underline mt-2 inline-flex items-center gap-1 font-semibold">
                                View all courses &rarr;
                            </span>
                        </div>
                    </div>
                </a>

                <!-- Attendance -->
                <a href="/student/attendance" class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm flex items-center space-x-4 hover:shadow-md hover:border-emerald-200 dark:hover:border-emerald-900/50 transition-all duration-200 block group">
                    <div class="flex items-center space-x-4 w-full">
                        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 group-hover:bg-emerald-100 dark:group-hover:bg-emerald-900/40 transition-colors duration-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z"/></svg>
                        </div>
                        <div>
                            <span class="text-xs text-gray-455 dark:text-gray-400 font-medium block">Attendance</span>
                            <span class="text-2xl font-bold text-gray-900 dark:text-white mt-1 block">{{ $attendancePercentage }}%</span>
                            <span class="text-xs text-emerald-600 group-hover:underline mt-2 inline-flex items-center gap-1 font-semibold">
                                View attendance logs &rarr;
                            </span>
                        </div>
                    </div>
                </a>

                <!-- Leave Applications -->
                <a href="/student/leave-applications" class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm flex items-center space-x-4 hover:shadow-md hover:border-purple-200 dark:hover:border-purple-900/50 transition-all duration-200 block group">
                    <div class="flex items-center space-x-4 w-full">
                        <div class="p-4 rounded-xl bg-purple-50 dark:bg-purple-950/30 text-purple-600 dark:text-purple-400 group-hover:bg-purple-100 dark:group-hover:bg-purple-900/40 transition-colors duration-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                        </div>
                        <div>
                            <span class="text-xs text-gray-455 dark:text-gray-400 font-medium block">Leave Applications</span>
                            <span class="text-2xl font-bold text-gray-900 dark:text-white mt-1 block">{{ $leaveApplicationsCount }}</span>
                            <span class="text-xs text-purple-600 group-hover:underline mt-2 inline-flex items-center gap-1 font-semibold">
                                View my applications &rarr;
                            </span>
                        </div>
                    </div>
                </a>

                <!-- Outstanding Fees -->
                <a href="/student/fee-installments" class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm flex items-center space-x-4 hover:shadow-md hover:border-amber-200 dark:hover:border-amber-900/50 transition-all duration-200 block group">
                    <div class="flex items-center space-x-4 w-full">
                        <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-950/30 text-amber-600 dark:text-amber-400 group-hover:bg-amber-100 dark:group-hover:bg-amber-900/40 transition-colors duration-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.752-.376a8.03 8.03 0 0 1 4.496 0L15 15.182m-6-4.82c.001-.004.003-.009.005-.013L9.75 10a8.03 8.03 0 0 1 4.496 0L15 10.364m-6 2.409a8.026 8.026 0 0 0 3 0m0-6V3m0 18v-3m0-6.75H12M9 13.5h.008v.008H9V13.5Zm0 2.25h.008v.008H9V15.75Zm0 2.25h.008v.008H9v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15.75Zm0 2.25h.008v.008h-.008v-.008Z"/></svg>
                        </div>
                        <div>
                            <span class="text-xs text-gray-455 dark:text-gray-400 font-medium block">Pending Dues</span>
                            <span class="text-2xl font-bold text-gray-900 dark:text-white mt-1 block">₹{{ number_format($pendingDues, 2) }}</span>
                            <span class="text-xs text-amber-600 group-hover:underline mt-2 inline-flex items-center gap-1 font-semibold">
                                View fee installments &rarr;
                            </span>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Content Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left: My Courses (ColSpan 2) -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white">My Courses</h2>
                            <a href="/student/enrollments" class="text-xs text-primary-600 hover:underline">View All</a>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm text-gray-500 dark:text-gray-400">
                                <thead class="bg-gray-50 dark:bg-gray-800 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">
                                    <tr>
                                        <th class="px-4 py-3">Course Code</th>
                                        <th class="px-4 py-3">Course Title</th>
                                        <th class="px-4 py-3">Instructor</th>
                                        <th class="px-4 py-3">Schedule</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                    @forelse($enrollments as $enrollment)
                                        <tr>
                                            <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">{{ $enrollment->course->course_code }}</td>
                                            <td class="px-4 py-3">{{ $enrollment->course->course_name }}</td>
                                            <td class="px-4 py-3">{{ $enrollment->instructor ? $enrollment->instructor->name : 'N/A' }}</td>
                                            <td class="px-4 py-3">{{ $enrollment->time_slot ?: 'Not Scheduled' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-4 py-6 text-center text-gray-450 dark:text-gray-500">Not enrolled in any courses yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Attendance Overview -->
                    <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Attendance Overview</h2>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                            <!-- Circular Progress SVG -->
                            <div class="flex justify-center py-4">
                                <div class="relative w-40 h-40">
                                    <!-- Donut Graph -->
                                    <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                                        <!-- Background Circle -->
                                        <circle class="text-gray-100 dark:text-gray-800" stroke-width="3" stroke="currentColor" fill="none" r="16" cx="18" cy="18"/>
                                        
                                        <!-- Present Circle Segment -->
                                        @php
                                            $presentPct = $attendanceBreakdown['Present'] ?? 0;
                                            $absentPct = $attendanceBreakdown['Absent'] ?? 0;
                                            $leavePct = $attendanceBreakdown['Leave'] ?? 0;
                                            $offset = 100;
                                        @endphp
                                        @if($presentPct > 0)
                                            <circle class="text-emerald-500" stroke-width="3" stroke-dasharray="{{ $presentPct }} 100" stroke-linecap="round" stroke="currentColor" fill="none" r="16" cx="18" cy="18"/>
                                            @php $offset -= $presentPct; @endphp
                                        @endif
                                    </svg>
                                    <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                                        <span class="text-3xl font-extrabold text-gray-900 dark:text-white">{{ $attendancePercentage }}%</span>
                                        <span class="text-[10px] uppercase text-gray-400 font-semibold mt-0.5">Overall</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Legend/Stats -->
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Present</span>
                                    </div>
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $attendanceBreakdown['Present'] }}%</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <span class="w-3 h-3 rounded-full bg-red-500"></span>
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Absent</span>
                                    </div>
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $attendanceBreakdown['Absent'] }}%</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Leave</span>
                                    </div>
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $attendanceBreakdown['Leave'] }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Enroll and Leave panels (ColSpan 1) -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Enroll in New Course Card -->
                    <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl p-6 shadow-sm text-center flex flex-col items-center justify-center min-h-[300px]">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white w-full text-left mb-4">Enroll in New Course</h2>
                        
                        <div class="relative mb-4 flex items-center justify-center">
                            <div class="w-20 h-20 bg-blue-50 dark:bg-blue-950/20 rounded-full flex items-center justify-center text-blue-600 dark:text-blue-400">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6 px-4">Explore and enroll in new courses to expand your skills.</p>
                        
                        <a href="/student/enrollments/create" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl shadow-md shadow-blue-500/10 transition duration-150">
                            Browse Courses
                        </a>
                    </div>

                    <!-- Leave Applications list & Apply button -->
                    <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl p-6 shadow-sm space-y-4">
                        <div class="flex items-center justify-between border-b dark:border-gray-800 pb-3">
                            <h2 class="text-md font-bold text-gray-900 dark:text-white">Leave Applications</h2>
                            <a href="/student/leave-applications" class="text-xs text-primary-600 hover:underline">View All</a>
                        </div>
                        
                        <div class="space-y-3">
                            @forelse($latestLeaves as $leave)
                                @php
                                    $leaveColor = match($leave->status) {
                                        'Approved' => 'success',
                                        'Pending' => 'warning',
                                        'Rejected' => 'danger',
                                        default => 'gray',
                                    };
                                @endphp
                                <div class="p-3 bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl space-y-1">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="font-bold text-gray-850 dark:text-gray-300">
                                            {{ $leave->start_date->format('M d, Y') }} - {{ $leave->end_date->format('M d, Y') }}
                                        </span>
                                        <span class="px-2 py-0.5 font-semibold text-[10px] rounded-full bg-{{ $leaveColor }}-100 text-{{ $leaveColor }}-800 dark:bg-{{ $leaveColor }}-500/10 dark:text-{{ $leaveColor }}-400">
                                            {{ $leave->status }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500 line-clamp-1">Reason: {{ $leave->reason }}</p>
                                </div>
                            @empty
                                <p class="text-xs text-gray-400 text-center py-4">No recent leave applications.</p>
                            @endforelse
                        </div>

                        <a href="/student/leave-applications/create" class="w-full inline-flex items-center justify-center px-4 py-2 bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 border border-gray-200 dark:border-gray-750 text-gray-700 dark:text-gray-300 font-semibold rounded-xl text-sm transition duration-150">
                            Apply for Leave
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>
