<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AdmissionResource\Pages;
use App\Models\Admission;
use App\Models\Course;
use App\Models\User;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Actions;
use Filament\Tables\Table;

class AdmissionResource extends Resource
{
    protected static ?string $model = Admission::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-identification';
    protected static string | \UnitEnum | null $navigationGroup = 'Student Relations';
    protected static ?int $navigationSort = 2;
    protected static ?string $modelLabel = 'Student Admission';
    protected static ?string $pluralModelLabel = 'Students';

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Student Personal Details')
                    ->schema([
                        Forms\Components\FileUpload::make('student_photo')
                            ->image()
                            ->avatar()
                            ->directory('student_photos')
                            ->columnSpanFull(),
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('student_name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('father_name')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('mobile')
                                    ->required()
                                    ->tel()
                                    ->maxLength(50),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->maxLength(255),
                            ]),
                        Forms\Components\Textarea::make('address')
                            ->rows(2)
                            ->maxLength(65535),
                    ])->columnSpanFull(),

                Section::make('Course Enrollments')
                    ->schema([
                        Forms\Components\DatePicker::make('admission_date')
                            ->required()
                            ->default(now()),
                        Forms\Components\Repeater::make('enrollments')
                            ->label('Enrollments')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        Forms\Components\Select::make('course_id')
                                            ->relationship('course', 'course_name')
                                            ->required()
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                if ($state) {
                                                    $course = Course::find($state);
                                                    if ($course) {
                                                        $set('total_fee', $course->total_fee + $course->registration_fee);
                                                        $set('registration_fee', $course->registration_fee);
                                                        $set('final_fee', ($course->total_fee + $course->registration_fee));
                                                    }
                                                } else {
                                                    $set('total_fee', 0);
                                                    $set('registration_fee', 0);
                                                    $set('final_fee', 0);
                                                }
                                            }),
                                        Forms\Components\TextInput::make('time_slot')
                                            ->label('Time Slot')
                                            ->required()
                                            ->placeholder('e.g. 09:00 AM - 11:00 AM'),
                                        Forms\Components\Select::make('instructor_id')
                                            ->label('Assigned Instructor')
                                            ->options(User::role('Instructor')->pluck('name', 'id'))
                                            ->searchable()
                                            ->required(),
                                    ]),
                                Grid::make(5)
                                    ->schema([
                                        Forms\Components\TextInput::make('total_fee')
                                            ->required()
                                            ->numeric()
                                            ->prefix('₹')
                                            ->readOnly(),
                                        Forms\Components\TextInput::make('registration_fee')
                                            ->required()
                                            ->numeric()
                                            ->prefix('₹')
                                            ->readOnly(),
                                        Forms\Components\TextInput::make('discount_amount')
                                            ->required()
                                            ->numeric()
                                            ->default(0.00)
                                            ->prefix('₹')
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                                $total = floatval($get('total_fee') ?? 0);
                                                $discount = floatval($state ?? 0);
                                                $set('final_fee', $total - $discount);
                                            }),
                                        Forms\Components\TextInput::make('final_fee')
                                            ->required()
                                            ->numeric()
                                            ->prefix('₹')
                                            ->readOnly(),
                                        Forms\Components\Select::make('status')
                                            ->required()
                                            ->options([
                                                'Active' => 'Active',
                                                'Hold' => 'Hold',
                                                'Completed' => 'Completed',
                                                'Cancelled' => 'Cancelled',
                                            ])
                                            ->default('Active'),
                                    ]),
                                Forms\Components\Hidden::make('id'),
                            ])
                            ->minItems(1)
                            ->columnSpanFull()
                    ])->columnSpanFull()
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('roll_no')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ImageColumn::make('student_photo')
                    ->circular()
                    ->label('Photo'),
                Tables\Columns\TextColumn::make('student_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('mobile')
                    ->searchable(),
                Tables\Columns\TextColumn::make('course.course_code')
                    ->label('Course')
                    ->sortable(),
                Tables\Columns\TextColumn::make('time_slot')
                    ->label('Time Slot')
                    ->sortable(),
                Tables\Columns\TextColumn::make('final_fee')
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
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Active' => 'Active',
                        'Hold' => 'Hold',
                        'Completed' => 'Completed',
                        'Cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\SelectFilter::make('course_id')
                    ->relationship('course', 'course_name'),
                Tables\Filters\SelectFilter::make('time_slot')
                    ->options(fn () => Admission::whereNotNull('time_slot')->distinct()->pluck('time_slot', 'time_slot')->toArray()),
            ])
            ->actions([
                Actions\ActionGroup::make([
                    Actions\ViewAction::make()
                        ->label('Profile')
                        ->icon('heroicon-o-user')
                        ->color('info'),
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
            // Relations will be shown in the View Profile page, which is cleaner
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdmissions::route('/'),
            'create' => Pages\CreateAdmission::route('/create'),
            'view' => Pages\ViewAdmission::route('/{record}'),
            'edit' => Pages\EditAdmission::route('/{record}/edit'),
        ];
    }
}
