<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\LeaveApplicationResource\Pages;
use App\Models\LeaveApplication;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Actions;
use Filament\Tables\Table;

class LeaveApplicationResource extends Resource
{
    protected static ?string $model = LeaveApplication::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-envelope';
    protected static string | \UnitEnum | null $navigationGroup = 'Academic Management';
    protected static ?int $navigationSort = 4;
    protected static ?string $modelLabel = 'Leave Application';

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Forms\Components\Select::make('admission_id')
                            ->relationship('admission', 'student_name')
                            ->searchable()
                            ->required()
                            ->disabled(fn ($record) => $record !== null)
                            ->dehydrated(),
                        Forms\Components\DatePicker::make('start_date')
                            ->required()
                            ->disabled(fn ($record) => $record !== null && $record->status !== 'Pending')
                            ->dehydrated(),
                        Forms\Components\DatePicker::make('end_date')
                            ->required()
                            ->disabled(fn ($record) => $record !== null && $record->status !== 'Pending')
                            ->dehydrated(),
                        Forms\Components\Textarea::make('reason')
                            ->required()
                            ->rows(3)
                            ->disabled(fn ($record) => $record !== null && $record->status !== 'Pending')
                            ->dehydrated()
                            ->columnSpanFull(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'Pending' => 'Pending',
                                'Approved' => 'Approved',
                                'Rejected' => 'Rejected',
                            ])
                            ->required()
                            ->default('Pending'),
                        Forms\Components\Textarea::make('admin_remarks')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('admission.student_name')
                    ->label('Student')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('admission.admission_no')
                    ->label('Admission No')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reason')
                    ->limit(50),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Approved' => 'success',
                        'Pending' => 'warning',
                        'Rejected' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('admin_remarks')
                    ->limit(30),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Pending' => 'Pending',
                        'Approved' => 'Approved',
                        'Rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                Actions\ActionGroup::make([
                    Actions\EditAction::make(),
                    
                    // Quick Approve Action
                    Actions\Action::make('approve')
                        ->label('Approve')
                        ->color('success')
                        ->icon('heroicon-o-check-circle')
                        ->visible(fn (LeaveApplication $record) => $record->status === 'Pending')
                        ->form([
                            Forms\Components\Textarea::make('admin_remarks')
                                ->label('Remarks')
                                ->rows(3),
                        ])
                        ->action(function (LeaveApplication $record, array $data) {
                            $record->update([
                                'status' => 'Approved',
                                'admin_remarks' => $data['admin_remarks'] ?? null,
                            ]);
                        }),

                    // Quick Reject Action
                    Actions\Action::make('reject')
                        ->label('Reject')
                        ->color('danger')
                        ->icon('heroicon-o-x-circle')
                        ->visible(fn (LeaveApplication $record) => $record->status === 'Pending')
                        ->form([
                            Forms\Components\Textarea::make('admin_remarks')
                                ->label('Remarks')
                                ->rows(3)
                                ->required(),
                        ])
                        ->action(function (LeaveApplication $record, array $data) {
                            $record->update([
                                'status' => 'Rejected',
                                'admin_remarks' => $data['admin_remarks'],
                            ]);
                        }),
                        
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageLeaveApplications::route('/'),
        ];
    }
}
