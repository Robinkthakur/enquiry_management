<?php

namespace App\Filament\Student\Resources;

use App\Filament\Student\Resources\EnrollmentResource\Pages;
use App\Models\AdmissionCourse;
use App\Models\Course;
use App\Models\User;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class EnrollmentResource extends Resource
{
    protected static ?string $model = AdmissionCourse::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'My Courses';
    protected static ?string $modelLabel = 'Course Enrollment';
    protected static ?string $pluralModelLabel = 'My Courses';
    protected static ?string $slug = 'enrollments';

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Select Course to Enroll')
                    ->schema([
                        Forms\Components\Select::make('course_id')
                            ->label('Select Course')
                            ->options(Course::where('status', 'active')->pluck('course_name', 'id'))
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $course = Course::find($state);
                                    if ($course) {
                                        $set('total_fee', "₹" . number_format($course->total_fee, 2));
                                    }
                                } else {
                                    $set('total_fee', null);
                                }
                            }),
                        Forms\Components\TextInput::make('total_fee')
                            ->label('Course Fee')
                            ->disabled()
                            ->dehydrated(false),
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TimePicker::make('start_time')
                                    ->label('Preferred Start Time')
                                    ->format('h:i A')
                                    ->displayFormat('h:i A')
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                        $endTime = $get('end_time');
                                        if ($state && $endTime) {
                                            $set('time_slot', "{$state} - {$endTime}");
                                        }
                                    }),
                                Forms\Components\TimePicker::make('end_time')
                                    ->label('Preferred End Time')
                                    ->format('h:i A')
                                    ->displayFormat('h:i A')
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                        $startTime = $get('start_time');
                                        if ($state && $startTime) {
                                            $set('time_slot', "{$startTime} - {$state}");
                                        }
                                    }),
                            ]),
                        Forms\Components\Hidden::make('time_slot'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('course.course_code')
                    ->label('Course Code')
                    ->fontFamily('mono')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('course.course_name')
                    ->label('Course Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('time_slot')
                    ->label('Time Slot')
                    ->placeholder('Not Scheduled'),
                Tables\Columns\TextColumn::make('instructor.name')
                    ->label('Instructor')
                    ->placeholder('Not Assigned'),
                Tables\Columns\TextColumn::make('final_fee')
                    ->label('Fees')
                    ->money('INR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Active' => 'success',
                        'Hold' => 'warning',
                        'Completed' => 'info',
                        'Cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Actions\ViewAction::make(),
            ])
            ->bulkActions([
                //
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $admission = Auth::user()->admission;
        if (!$admission) {
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }
        return parent::getEloquentQuery()->where('admission_id', $admission->id);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEnrollments::route('/'),
            'create' => Pages\CreateEnrollment::route('/create'),
            'view' => Pages\ViewEnrollment::route('/{record}'),
        ];
    }
}
