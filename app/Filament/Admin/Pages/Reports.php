<?php

namespace App\Filament\Admin\Pages;

use App\Models\Course;
use App\Models\User;
use App\Models\Enquiry;
use App\Models\Admission;
use App\Models\Attendance;
use App\Models\FeePayment;
use App\Models\FeeInstallment;
use App\Models\CompanySetting;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Forms;
use Filament\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\DB;

class Reports extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static string | \UnitEnum | null $navigationGroup = 'Academic Management';
    protected static ?int $navigationSort = 5;
    protected static ?string $title = 'Advanced Reports';

    protected string $view = 'filament.admin.pages.reports';

    public ?string $report_type = 'enquiries';
    public ?string $start_date = null;
    public ?string $end_date = null;
    public ?string $course_id = null;
    public ?string $time_slot = null;

    public function mount(): void
    {
        $this->start_date = now()->startOfMonth()->format('Y-m-d');
        $this->end_date = now()->endOfMonth()->format('Y-m-d');
        $this->form->fill([
            'report_type' => $this->report_type,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Grid::make(3)
                    ->schema([
                        Forms\Components\Select::make('report_type')
                            ->label('Report Type')
                            ->required()
                            ->options([
                                'enquiries' => '1. Enquiry Report',
                                'admissions' => '2. Admission Report',
                                'students' => '3. Student List Report',
                                'attendance' => '4. Attendance Report',
                                'fee_collection' => '5. Fee Collection Report',
                                'due_fees' => '6. Due Fee Report',
                                'courses' => '7. Course Report',
                                'instructors' => '8. Instructor Report',
                            ])
                            ->reactive(),
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Start Date')
                            ->default(now()->startOfMonth()),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('End Date')
                            ->default(now()->endOfMonth()),
                    ]),
                Grid::make(2)
                    ->schema([
                        Forms\Components\Select::make('course_id')
                            ->label('Course Filter (Optional)')
                            ->options(Course::pluck('course_name', 'id'))
                            ->nullable()
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set) => $set('time_slot', null)),
                        Forms\Components\Select::make('time_slot')
                            ->label('Time Slot Filter (Optional)')
                            ->options(function (callable $get) {
                                $courseId = $get('course_id');
                                $query = Admission::whereNotNull('time_slot')->distinct();
                                if ($courseId) {
                                    $query->where('course_id', $courseId);
                                }
                                return $query->pluck('time_slot', 'time_slot')->toArray();
                            })
                            ->nullable(),
                    ])
            ]);
    }

    /**
     * Fetch records based on filters.
     */
    protected function getReportData(): array
    {
        $state = $this->form->getState();
        $type = $state['report_type'] ?? 'enquiries';
        $start = $state['start_date'] ? $state['start_date'] . ' 00:00:00' : now()->startOfMonth()->toDateTimeString();
        $end = $state['end_date'] ? $state['end_date'] . ' 23:59:59' : now()->endOfMonth()->toDateTimeString();
        $courseId = $state['course_id'] ?? null;
        $timeSlot = $state['time_slot'] ?? null;

        $headers = [];
        $rows = [];

        switch ($type) {
            case 'enquiries':
                $headers = ['Enquiry No', 'Name', 'Mobile', 'Email', 'Source', 'Follow Up', 'Status', 'Date'];
                $query = Enquiry::whereBetween('created_at', [$start, $end]);
                if ($courseId) {
                    $query->whereHas('interestedCourses', fn ($q) => $q->where('course_id', $courseId));
                }
                $records = $query->get();
                foreach ($records as $r) {
                    $rows[] = [
                        $r->enquiry_no, $r->name, $r->mobile, $r->email ?: 'N/A', 
                        $r->enquiry_source, $r->follow_up_date ? $r->follow_up_date->format('Y-m-d') : 'N/A', 
                        $r->status, $r->created_at->format('Y-m-d')
                    ];
                }
                break;

            case 'admissions':
                $headers = ['Admission No', 'Roll No', 'Student Name', 'Mobile', 'Course', 'Time Slot', 'Final Fee', 'Date'];
                $query = Admission::whereBetween('admission_date', [$start, $end]);
                if ($courseId) $query->where('course_id', $courseId);
                if ($timeSlot) $query->where('time_slot', $timeSlot);
                $records = $query->get();
                foreach ($records as $r) {
                    $rows[] = [
                        $r->admission_no, $r->roll_no, $r->student_name, $r->mobile, 
                        $r->course->course_code, $r->time_slot ?: 'N/A', '₹' . number_format($r->final_fee, 2), 
                        $r->admission_date->format('Y-m-d')
                    ];
                }
                break;

            case 'students':
                $headers = ['Roll No', 'Student Name', 'Mobile', 'Course', 'Time Slot', 'Attendance %', 'Status'];
                $query = Admission::query();
                if ($courseId) $query->where('course_id', $courseId);
                if ($timeSlot) $query->where('time_slot', $timeSlot);
                $records = $query->get();
                foreach ($records as $r) {
                    $rows[] = [
                        $r->roll_no, $r->student_name, $r->mobile, 
                        $r->course->course_code, $r->time_slot ?: 'N/A', 
                        $r->attendance_percentage . '%', $r->status
                    ];
                }
                break;

            case 'attendance':
                $headers = ['Date', 'Time Slot', 'Student Name', 'Roll No', 'Status'];
                $query = Attendance::whereBetween('attendance_date', [$start, $end]);
                if ($timeSlot) {
                    $query->whereHas('student', fn ($q) => $q->where('time_slot', $timeSlot));
                }
                if ($courseId) {
                    $query->whereHas('student', fn ($q) => $q->where('course_id', $courseId));
                }
                $records = $query->with(['student.course'])->get();
                foreach ($records as $r) {
                    $rows[] = [
                        $r->attendance_date->format('Y-m-d'), $r->student->time_slot ?: 'N/A', 
                        $r->student->student_name, $r->student->roll_no, $r->status
                    ];
                }
                break;

            case 'fee_collection':
                $headers = ['Receipt No', 'Student Name', 'Roll No', 'Payment Method', 'Amount Paid', 'Date'];
                $query = FeePayment::whereBetween('receipt_date', [$start, $end]);
                if ($courseId || $timeSlot) {
                    $query->whereHas('admission', function ($q) use ($courseId, $timeSlot) {
                        if ($courseId) $q->where('course_id', $courseId);
                        if ($timeSlot) $q->where('time_slot', $timeSlot);
                    });
                }
                $records = $query->with('admission')->get();
                foreach ($records as $r) {
                    $rows[] = [
                        $r->receipt_no, $r->admission->student_name, $r->admission->roll_no, 
                        $r->payment_method, '₹' . number_format($r->amount_paid, 2), 
                        $r->receipt_date->format('Y-m-d')
                    ];
                }
                break;

            case 'due_fees':
                $headers = ['Student Name', 'Roll No', 'Inst No', 'Due Date', 'Amount Due', 'Status'];
                $query = FeeInstallment::whereIn('status', ['Pending', 'Partial', 'Overdue'])
                    ->whereBetween('due_date', [$start, $end]);
                if ($courseId || $timeSlot) {
                    $query->whereHas('admission', function ($q) use ($courseId, $timeSlot) {
                        if ($courseId) $q->where('course_id', $courseId);
                        if ($timeSlot) $q->where('time_slot', $timeSlot);
                    });
                }
                $records = $query->with('admission')->get();
                foreach ($records as $r) {
                    $rows[] = [
                        $r->admission->student_name, $r->admission->roll_no, $r->installment_no, 
                        $r->due_date->format('Y-m-d'), '₹' . number_format($r->due_amount, 2), $r->status
                    ];
                }
                break;

            case 'courses':
                $headers = ['Course Code', 'Course Name', 'Duration', 'Total Fee', 'Reg Fee', 'Status'];
                $records = Course::all();
                foreach ($records as $r) {
                    $rows[] = [
                        $r->course_code, $r->course_name, $r->duration_months . ' Mos', 
                        '₹' . number_format($r->total_fee, 2), '₹' . number_format($r->registration_fee, 2), 
                        $r->status
                    ];
                }
                break;

            case 'instructors':
                $headers = ['Instructor Name', 'Email', 'Active Students'];
                $records = User::role('Instructor')->withCount([
                    'admissions' => fn ($q) => $q->where('status', 'Active')
                ])->get();
                foreach ($records as $r) {
                    $rows[] = [
                        $r->name, $r->email, $r->admissions_count
                    ];
                }
                break;
        }

        return compact('headers', 'rows', 'type');
    }

    /**
     * Download report as CSV.
     */
    public function exportCsv(): StreamedResponse
    {
        $data = $this->getReportData();

        return response()->streamDownload(function () use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $data['headers']);
            foreach ($data['rows'] as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        }, 'report_' . $data['type'] . '_' . now()->format('YmdHis') . '.csv');
    }

    /**
     * Download report as PDF using DomPDF.
     */
    public function exportPdf(): StreamedResponse
    {
        $data = $this->getReportData();
        $title = strtoupper($data['type']) . ' REPORT';
        
        $setting = CompanySetting::first();

        $pdf = Pdf::loadView('pdf.report', compact('data', 'title', 'setting'))
            ->setPaper('a4', 'landscape');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'report_' . $data['type'] . '_' . now()->format('YmdHis') . '.pdf'
        );
    }
}
