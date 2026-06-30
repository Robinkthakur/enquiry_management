<?php

namespace App\Filament\Admin\Pages;

use App\Models\Course;
use App\Models\Admission;
use App\Models\AdmissionCourse;
use App\Models\Attendance;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Forms;
use Filament\Notifications\Notification;

class MarkAttendance extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-pencil-square';
    protected static string | \UnitEnum | null $navigationGroup = 'Academic Management';
    protected static ?int $navigationSort = 4;
    protected static ?string $title = 'Mark Attendance';

    protected string $view = 'filament.admin.pages.mark-attendance';

    public ?string $attendance_date = null;
    public array $attendance_statuses = []; // [admission_course_id => status]
    public ?string $search = '';

    public function mount(): void
    {
        $this->attendance_date = now()->format('Y-m-d');
        $this->form->fill([
            'attendance_date' => $this->attendance_date,
        ]);
        $this->loadStudents();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('attendance_date')
                    ->label('Date')
                    ->required()
                    ->default(now())
                    ->reactive()
                    ->afterStateUpdated(fn ($state) => $this->loadExistingAttendance()),
            ]);
    }

    public function loadStudents(): void
    {
        $this->attendance_statuses = [];

        $query = AdmissionCourse::with('admission')
            ->where('status', 'Active');
            
        if (auth()->user()->hasRole('Instructor')) {
            $query->where('instructor_id', auth()->id());
        }
        
        $enrollments = $query->get();
        
        $enrollmentIds = $enrollments->pluck('id')->toArray();
        
        $existing = Attendance::whereIn('admission_course_id', $enrollmentIds)
            ->where('attendance_date', $this->attendance_date)
            ->pluck('status', 'admission_course_id')
            ->toArray();

        foreach ($enrollments as $enrollment) {
            $this->attendance_statuses[$enrollment->id] = $existing[$enrollment->id] ?? 'Present';
        }
    }

    public function loadExistingAttendance(): void
    {
        $state = $this->form->getState();
        $this->attendance_date = $state['attendance_date'] ?? now()->format('Y-m-d');
        $this->loadStudents();
    }

    public function getStudentsProperty()
    {
        $query = AdmissionCourse::with('admission')
            ->where('status', 'Active');
            
        if (auth()->user()->hasRole('Instructor')) {
            $query->where('instructor_id', auth()->id());
        }

        if (filled($this->search)) {
            $query->whereHas('admission', function ($q) {
                $q->where('student_name', 'like', '%' . $this->search . '%')
                  ->orWhere('admission_no', 'like', '%' . $this->search . '%');
            });
        }
        
        return $query->get();
    }

    public function setStatus(string $studentId, string $status): void
    {
        $this->attendance_statuses[$studentId] = $status;
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $date = $state['attendance_date'] ?? now()->format('Y-m-d');

        foreach ($this->attendance_statuses as $enrollmentId => $status) {
            $enrollment = AdmissionCourse::find($enrollmentId);
            if ($enrollment) {
                Attendance::updateOrCreate(
                    [
                        'admission_course_id' => $enrollmentId,
                        'attendance_date' => $date,
                    ],
                    [
                        'admission_id' => $enrollment->admission_id,
                        'status' => $status,
                    ]
                );
            }
        }

        Notification::make()
            ->title('Attendance Saved Successfully')
            ->body('Daily attendance has been recorded.')
            ->success()
            ->send();
    }
}
