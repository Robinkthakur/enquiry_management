<?php

namespace App\Filament\Student\Pages;

use Filament\Pages\Page;
use App\Models\Admission;
use App\Models\AdmissionCourse;
use App\Models\LeaveApplication;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;

class StudentDashboard extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-home';

    protected string $view = 'filament.student.pages.student-dashboard';

    protected static ?string $title = 'Dashboard';

    protected static ?string $slug = 'dashboard';

    public ?Admission $admission = null;
    public int $enrolledCoursesCount = 0;
    public float $attendancePercentage = 0.0;
    public int $leaveApplicationsCount = 0;
    public float $pendingDues = 0.0;
    public $enrollments = [];
    public $latestLeaves = [];
    public array $attendanceBreakdown = ['Present' => 0, 'Absent' => 0, 'Leave' => 0];

    public function mount(): void
    {
        $user = Auth::user();
        if ($user) {
            $this->admission = $user->admission;
            if ($this->admission) {
                // Enrolled courses count
                $this->enrolledCoursesCount = $this->admission->enrollments()->count();
                
                // Attendance percentage
                $this->attendancePercentage = $this->admission->attendance_percentage;
                
                // Leave applications count
                $this->leaveApplicationsCount = $this->admission->leaveApplications()->count();
                
                // Total final fees vs total paid fees
                $totalPaid = $this->admission->payments()->sum('amount_paid');
                $this->pendingDues = max(0.00, $this->admission->final_fee - $totalPaid);
                
                // Active Enrollments
                $this->enrollments = $this->admission->enrollments()->with('course')->get();
                
                // Latest 3 leave applications
                $this->latestLeaves = $this->admission->leaveApplications()->latest()->take(3)->get();
                
                // Attendance breakdown for donut chart
                $attendances = $this->admission->attendances;
                $totalCount = $attendances->count();
                if ($totalCount > 0) {
                    $presentCount = $attendances->where('status', 'Present')->count();
                    $absentCount = $attendances->where('status', 'Absent')->count();
                    $leaveCount = $attendances->where('status', 'Leave')->count();
                    
                    $this->attendanceBreakdown = [
                        'Present' => round(($presentCount / $totalCount) * 100),
                        'Absent' => round(($absentCount / $totalCount) * 100),
                        'Leave' => round(($leaveCount / $totalCount) * 100),
                    ];
                }
            }
        }
    }
}
