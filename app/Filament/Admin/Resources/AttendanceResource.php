<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AttendanceResource\Pages;
use App\Models\Attendance;
use App\Models\AdmissionCourse;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Actions;
use Filament\Tables\Table;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-check-circle';
    protected static string | \UnitEnum | null $navigationGroup = 'Academic Management';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Forms\Components\Select::make('admission_course_id')
                            ->label('Student Course Enrollment')
                            ->options(function () {
                                return AdmissionCourse::where('status', 'Active')
                                    ->with(['admission', 'course'])
                                    ->get()
                                    ->mapWithKeys(function ($enrollment) {
                                        $label = "{$enrollment->admission->student_name} - {$enrollment->course->course_code} (" . ($enrollment->time_slot ?: 'No Slot') . ")";
                                        return [$enrollment->id => $label];
                                    })->toArray();
                            })
                            ->required()
                            ->searchable()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $enrollment = AdmissionCourse::find($state);
                                    if ($enrollment) {
                                        $set('admission_id', $enrollment->admission_id);
                                    }
                                } else {
                                    $set('admission_id', null);
                                }
                            }),
                        Forms\Components\Hidden::make('admission_id'),
                        Forms\Components\DatePicker::make('attendance_date')
                            ->required()
                            ->default(now()),
                        Forms\Components\Select::make('status')
                            ->options([
                                'Present' => 'Present',
                                'Absent' => 'Absent',
                                'Leave' => 'Leave',
                            ])
                            ->required(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('attendance_date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('student.student_name')
                    ->label('Student')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('enrollment.course.course_code')
                    ->label('Course')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Present' => 'success',
                        'Absent' => 'danger',
                        'Leave' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('course_id')
                    ->label('Course')
                    ->relationship('enrollment.course', 'course_name'),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Present' => 'Present',
                        'Absent' => 'Absent',
                        'Leave' => 'Leave',
                    ]),
            ])
            ->actions([
                Actions\ActionGroup::make([
                    Actions\EditAction::make(),
                    Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }

    /**
     * Scope query so Instructors only see attendance records for their assigned batches.
     */
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->check() && auth()->user()->hasRole('Instructor')) {
            return $query->whereHas('enrollment', function ($q) {
                $q->where('instructor_id', auth()->id());
            });
        }

        return $query;
    }
}
