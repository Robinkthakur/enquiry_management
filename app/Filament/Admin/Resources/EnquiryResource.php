<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\EnquiryResource\Pages;
use App\Models\Enquiry;
use App\Models\Course;
use App\Models\Admission;
use App\Models\User;
use App\Models\FeeInstallment;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

use Filament\Actions;

class EnquiryResource extends Resource
{
    protected static ?string $model = Enquiry::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static string | \UnitEnum | null $navigationGroup = 'Student Relations';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
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
                                Forms\Components\Select::make('gender')
                                    ->options([
                                        'Male' => 'Male',
                                        'Female' => 'Female',
                                        'Other' => 'Other',
                                    ]),
                                Forms\Components\DatePicker::make('date_of_birth'),
                            ]),
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('qualification')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('occupation')
                                    ->maxLength(255),
                            ]),
                        Forms\Components\Textarea::make('address')
                            ->rows(2)
                            ->maxLength(65535),
                    ])->columnSpan(2),

                Section::make()
                    ->schema([
                        Forms\Components\Select::make('interestedCourses')
                            ->multiple()
                            ->relationship('interestedCourses', 'course_name')
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('enquiry_source')
                            ->required()
                            ->options([
                                'Google Search' => 'Google Search',
                                'Facebook Ad' => 'Facebook Ad',
                                'Instagram Ad' => 'Instagram Ad',
                                'Referral' => 'Referral',
                                'Walk-in' => 'Walk-in',
                                'Other' => 'Other',
                            ]),
                        Forms\Components\Select::make('status')
                            ->required()
                            ->options([
                                'New' => 'New',
                                'Follow Up' => 'Follow Up',
                                'Interested' => 'Interested',
                                'Not Interested' => 'Not Interested',
                                'Admitted' => 'Admitted',
                            ])
                            ->default('New'),
                        Forms\Components\DatePicker::make('follow_up_date'),
                        Forms\Components\Select::make('taken_by')
                            ->relationship('counselor', 'name')
                            ->default(fn () => auth()->id())
                            ->required(),
                        Forms\Components\Textarea::make('remarks')
                            ->rows(3)
                            ->maxLength(65535),
                    ])->columnSpan(1),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('enquiry_no')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('mobile')
                    ->searchable(),
                Tables\Columns\TextColumn::make('interestedCourses.course_code')
                    ->badge()
                    ->label('Interested Courses'),
                Tables\Columns\TextColumn::make('follow_up_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'New' => 'info',
                        'Follow Up' => 'warning',
                        'Interested' => 'success',
                        'Not Interested' => 'danger',
                        'Admitted' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('counselor.name')
                    ->label('Taken By')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'New' => 'New',
                        'Follow Up' => 'Follow Up',
                        'Interested' => 'Interested',
                        'Not Interested' => 'Not Interested',
                        'Admitted' => 'Admitted',
                    ]),
                Tables\Filters\SelectFilter::make('enquiry_source')
                    ->options([
                        'Google Search' => 'Google Search',
                        'Facebook Ad' => 'Facebook Ad',
                        'Instagram Ad' => 'Instagram Ad',
                        'Referral' => 'Referral',
                        'Walk-in' => 'Walk-in',
                    ]),
            ])
            ->actions([
                Actions\ActionGroup::make([
                    Actions\EditAction::make(),
                    
                    // WhatsApp Button
                    Actions\Action::make('whatsapp')
                        ->label('WhatsApp')
                        ->color('success')
                        ->icon('heroicon-o-phone')
                        ->url(fn (Enquiry $record) => "https://wa.me/" . preg_replace('/[^0-9]/', '', $record->mobile) . "?text=" . urlencode("Hello " . $record->name . ", this is regarding your enquiry at our institute."))
                        ->openUrlInNewTab(),

                    // Email Button
                    Actions\Action::make('email')
                        ->label('Email')
                        ->color('info')
                        ->icon('heroicon-o-envelope')
                        ->url(fn (Enquiry $record) => $record->email ? "mailto:" . $record->email . "?subject=" . urlencode("Enquiry Follow Up") . "&body=" . urlencode("Hello " . $record->name . ",\n\n") : null)
                        ->visible(fn (Enquiry $record) => !empty($record->email))
                        ->openUrlInNewTab(),

                    // Convert to Admission Action
                    Actions\Action::make('convert_to_admission')
                        ->label('Convert to Admission')
                        ->color('primary')
                        ->icon('heroicon-o-user-plus')
                        ->visible(fn (Enquiry $record) => $record->status !== 'Admitted')
                        ->form([
                            Forms\Components\DatePicker::make('admission_date')
                                ->required()
                                ->default(now()),
                            Forms\Components\Repeater::make('enrollments')
                                ->label('Course Enrollments')
                                ->schema([
                                    Forms\Components\Select::make('course_id')
                                        ->label('Select Course')
                                        ->required()
                                        ->options(fn (Enquiry $record) => $record->interestedCourses->pluck('course_name', 'id')->toArray() ?: Course::where('status', 'active')->pluck('course_name', 'id')->toArray())
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, callable $set) {
                                            if ($state) {
                                                $course = Course::find($state);
                                                if ($course) {
                                                    $set('total_fee', $course->total_fee);
                                                }
                                            } else {
                                                $set('total_fee', 0);
                                            }
                                        }),
                                    Forms\Components\TimePicker::make('start_time')
                                        ->label('Start Time')
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
                                        ->label('End Time')
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
                                    Forms\Components\Select::make('instructor_id')
                                        ->label('Assigned Instructor')
                                        ->options(\App\Models\Admin::role('Instructor')->pluck('name', 'id'))
                                        ->searchable()
                                        ->required(),
                                    Forms\Components\TextInput::make('total_fee')
                                        ->label('Total Course Fee')
                                        ->required()
                                        ->numeric()
                                        ->prefix('₹'),
                                    Forms\Components\Hidden::make('time_slot'),
                                ])
                                ->minItems(1)
                        ])
                        ->action(function (Enquiry $record, array $data) {
                            DB::transaction(function () use ($record, $data) {
                                $admissionDate = $data['admission_date'];
                                
                                // Create single student profile (Admission)
                                $admission = Admission::create([
                                    'enquiry_id' => $record->id,
                                    'student_name' => $record->name,
                                    'father_name' => $record->father_name,
                                    'mobile' => $record->mobile,
                                    'email' => $record->email,
                                    'address' => $record->address,
                                    'admission_date' => $admissionDate,
                                    'status' => 'Active',
                                ]);
                                
                                foreach ($data['enrollments'] as $enrollment) {
                                    $course = Course::findOrFail($enrollment['course_id']);
                                    $fee = floatval($enrollment['total_fee'] ?? $course->total_fee);

                                    // Create enrollment in admission_courses
                                    $admission->enrollments()->create([
                                        'course_id' => $course->id,
                                        'time_slot' => $enrollment['time_slot'],
                                        'instructor_id' => $enrollment['instructor_id'],
                                        'total_fee' => $fee,
                                        'discount_amount' => 0.00,
                                        'final_fee' => $fee,
                                        'registration_fee' => 0.00,
                                        'status' => 'Active',
                                    ]);
                                }
 
                                // Update Enquiry
                                $record->update(['status' => 'Admitted']);
                            });

                            Notification::make()
                                ->title('Conversion Successful')
                                ->body('The enquiry has been successfully converted into admissions.')
                                ->success()
                                ->send();
                        }),
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
            EnquiryResource\RelationManagers\TimelineRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEnquiries::route('/'),
            'create' => Pages\CreateEnquiry::route('/create'),
            'edit' => Pages\EditEnquiry::route('/{record}/edit'),
        ];
    }
}
