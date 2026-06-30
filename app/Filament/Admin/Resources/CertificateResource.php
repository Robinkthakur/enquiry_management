<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CertificateResource\Pages;
use App\Models\Certificate;
use App\Models\Admission;
use App\Models\Course;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Actions;
use Filament\Tables\Table;

class CertificateResource extends Resource
{
    protected static ?string $model = Certificate::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-academic-cap';
    protected static string | \UnitEnum | null $navigationGroup = 'Student Relations';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Forms\Components\Select::make('admission_id')
                            ->relationship('admission', 'student_name')
                            ->required()
                            ->reactive()
                            ->searchable()
                            ->default(request()->query('admission_id'))
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('course_id', null);
                            }),
                        Forms\Components\Select::make('course_id')
                            ->label('Course')
                            ->options(function (callable $get) {
                                $admissionId = $get('admission_id');
                                if (!$admissionId) {
                                    return [];
                                }
                                $admission = Admission::find($admissionId);
                                if (!$admission) {
                                    return [];
                                }
                                return $admission->courses->pluck('course_name', 'id')->toArray();
                            })
                            ->required()
                            ->default(request()->query('course_id')),
                        Forms\Components\DatePicker::make('issue_date')
                            ->required()
                            ->default(now()),
                        Forms\Components\DatePicker::make('completion_date')
                            ->required()
                            ->default(now()),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('certificate_no')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('admission.student_name')
                    ->label('Student')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('course.course_name')
                    ->label('Course')
                    ->sortable(),
                Tables\Columns\TextColumn::make('issue_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('completion_date')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('course_id')
                    ->relationship('course', 'course_name'),
            ])
            ->actions([
                Actions\ActionGroup::make([
                    Actions\EditAction::make(),
                    
                    // Print/Download Certificate PDF
                    Actions\Action::make('download_pdf')
                        ->label('PDF')
                        ->color('primary')
                        ->icon('heroicon-o-document-arrow-down')
                        ->url(fn (Certificate $record) => "/admin/certificates/{$record->id}/pdf")
                        ->openUrlInNewTab(),

                    // Public Verify Link
                    Actions\Action::make('verify')
                        ->label('Verify')
                        ->color('success')
                        ->icon('heroicon-o-arrow-top-right-on-square')
                        ->url(fn (Certificate $record) => "/verify-certificate/{$record->verification_token}")
                        ->openUrlInNewTab(),
                ]),
            ])
            ->bulkActions([
                //
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCertificates::route('/'),
        ];
    }
}
