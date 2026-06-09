<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CourseResource\Pages;
use App\Models\Course;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Actions;
use Filament\Tables\Table;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-academic-cap';
    protected static string | \UnitEnum | null $navigationGroup = 'Academic Management';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('course_code')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(50),
                                Forms\Components\TextInput::make('course_name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('duration_months')
                                    ->required()
                                    ->numeric()
                                    ->minValue(1),
                                Forms\Components\Select::make('status')
                                    ->required()
                                    ->options([
                                        'active' => 'Active',
                                        'inactive' => 'Inactive',
                                    ])
                                    ->default('active'),
                            ]),
                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])->columnSpan(2),

                Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('total_fee')
                            ->required()
                            ->numeric()
                            ->prefix('₹'),
                        Forms\Components\TextInput::make('registration_fee')
                            ->required()
                            ->numeric()
                            ->prefix('₹'),
                        Forms\Components\TextInput::make('certificate_fee')
                            ->required()
                            ->numeric()
                            ->prefix('₹'),
                        Forms\Components\TextInput::make('tax_percentage')
                            ->required()
                            ->numeric()
                            ->default(0.00)
                            ->suffix('%'),
                    ])->columnSpan(1),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('course_code')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('course_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration_months')
                    ->suffix(' months')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_fee')
                    ->money('INR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('registration_fee')
                    ->money('INR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('certificate_fee')
                    ->money('INR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ]),
            ])
            ->headerActions([
                Actions\Action::make('export')
                    ->label('Export CSV')
                    ->color('success')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn () => self::exportCsv()),
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
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
        ];
    }

    protected static function exportCsv(): StreamedResponse
    {
        $records = Course::all();
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="courses_' . now()->format('YmdHis') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($records) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'ID', 'Course Code', 'Course Name', 'Description', 
                'Duration (Months)', 'Total Fee', 'Registration Fee', 
                'Certificate Fee', 'Tax Percentage', 'Status'
            ]);

            foreach ($records as $record) {
                fputcsv($file, [
                    $record->id,
                    $record->course_code,
                    $record->course_name,
                    $record->description,
                    $record->duration_months,
                    $record->total_fee,
                    $record->registration_fee,
                    $record->certificate_fee,
                    $record->tax_percentage,
                    $record->status,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
