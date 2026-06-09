<?php

namespace App\Filament\Admin\Pages;

use App\Models\Course;
use App\Models\Admission;
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

    public ?string $course_id = null;
    public ?string $time_slot = null;
    public ?string $attendance_date = null;
    public array $attendance_statuses = []; // [admission_id => status]

    public function mount(): void
    {
        $this->attendance_date = now()->format('Y-m-d');
        $this->form->fill([
            'attendance_date' => $this->attendance_date,
        ]);
    }

    public function form(Schema $form): Schema
    {
        $coursesQuery = Course::where('status', 'active');
        if (auth()->user()->hasRole('Instructor')) {
            $coursesQuery->whereHas('admissions', function ($q) {
                $q->where('instructor_id', auth()->id())->where('status', 'Active');
            });
        }

        return $form
            ->schema([
                Grid::make(3)
                    ->schema([
                        Forms\Components\Select::make('course_id')
                            ->label('Select Course')
                            ->required()
                            ->options($coursesQuery->pluck('course_name', 'id'))
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $this->course_id = $state;
                                $set('time_slot', null);
                                $this->time_slot = null;
                                $this->loadStudents();
                            }),
                        Forms\Components\Select::make('time_slot')
                            ->label('Select Time Slot')
                            ->required()
                            ->options(function (callable $get) {
                                $courseId = $get('course_id');
                                if (!$courseId) {
                                    return [];
                                }
                                $query = Admission::where('course_id', $courseId)
                                    ->where('status', 'Active')
                                    ->whereNotNull('time_slot');
                                if (auth()->user()->hasRole('Instructor')) {
                                    $query->where('instructor_id', auth()->id());
                                }
                                return $query->distinct()->pluck('time_slot', 'time_slot')->toArray();
                            })
                            ->reactive()
                            ->afterStateUpdated(function ($state) {
                                $this->time_slot = $state;
                                $this->loadStudents();
                            }),
                        Forms\Components\DatePicker::make('attendance_date')
                            ->label('Date')
                            ->required()
                            ->default(now())
                            ->reactive()
                            ->afterStateUpdated(fn ($state) => $this->loadExistingAttendance()),
                    ])
            ]);
    }

    public function loadStudents(): void
    {
        $this->attendance_statuses = [];
        if (!$this->course_id || !$this->time_slot) {
            return;
        }

        $query = Admission::where('course_id', $this->course_id)
            ->where('time_slot', $this->time_slot)
            ->where('status', 'Active');
            
        if (auth()->user()->hasRole('Instructor')) {
            $query->where('instructor_id', auth()->id());
        }
        
        $students = $query->get();
        
        $studentIds = $students->pluck('id')->toArray();
        
        $existing = Attendance::whereIn('admission_id', $studentIds)
            ->where('attendance_date', $this->attendance_date)
            ->pluck('status', 'admission_id')
            ->toArray();

        foreach ($students as $student) {
            $this->attendance_statuses[$student->id] = $existing[$student->id] ?? 'Present';
        }
    }

    public function loadExistingAttendance(): void
    {
        $state = $this->form->getState();
        $this->attendance_date = $state['attendance_date'] ?? now()->format('Y-m-d');
        $this->course_id = $state['course_id'] ?? null;
        $this->time_slot = $state['time_slot'] ?? null;
        $this->loadStudents();
    }

    public function getStudentsProperty()
    {
        if (!$this->course_id || !$this->time_slot) {
            return collect();
        }
        $query = Admission::where('course_id', $this->course_id)
            ->where('time_slot', $this->time_slot)
            ->where('status', 'Active');
            
        if (auth()->user()->hasRole('Instructor')) {
            $query->where('instructor_id', auth()->id());
        }
        
        return $query->get();
    }

    public function setStatus(string $studentId, string $status): void
    {
        $this->attendance_statuses[$studentId] = $status;
    }

    public function save(): void
    {
        if (!$this->course_id || !$this->time_slot) {
            Notification::make()->title('Please select course and time slot first.')->danger()->send();
            return;
        }

        $state = $this->form->getState();
        $date = $state['attendance_date'] ?? now()->format('Y-m-d');

        foreach ($this->attendance_statuses as $studentId => $status) {
            Attendance::updateOrCreate(
                [
                    'admission_id' => $studentId,
                    'attendance_date' => $date,
                ],
                [
                    'status' => $status,
                ]
            );
        }

        Notification::make()
            ->title('Attendance Saved Successfully')
            ->body('Daily attendance has been recorded.')
            ->success()
            ->send();
    }
}
