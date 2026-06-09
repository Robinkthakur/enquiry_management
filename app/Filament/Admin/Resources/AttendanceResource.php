<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AttendanceResource\Pages;
use App\Models\Attendance;
use App\Models\Admission;
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
                        Forms\Components\Select::make('admission_id')
                            ->label('Student')
                            ->options(function () {
                                return Admission::where('status', 'Active')
                                    ->get()
                                    ->mapWithKeys(function ($admission) {
                                        $label = "{$admission->student_name} ({$admission->course->course_code} - " . ($admission->time_slot ?: 'No Slot') . ")";
                                        return [$admission->id => $label];
                                    })->toArray();
                            })
                            ->required()
                            ->searchable(),
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
                    ->relationship('student.course', 'course_name'),
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
            ]);
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
            return $query->whereHas('student', function ($q) {
                $q->where('instructor_id', auth()->id());
            });
        }

        return $query;
    }
}
