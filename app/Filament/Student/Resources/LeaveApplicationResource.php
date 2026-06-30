<?php

namespace App\Filament\Student\Resources;

use App\Filament\Student\Resources\LeaveApplicationResource\Pages;
use App\Models\LeaveApplication;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class LeaveApplicationResource extends Resource
{
    protected static ?string $model = LeaveApplication::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $navigationLabel = 'Leave Applications';
    protected static ?string $modelLabel = 'Leave Application';
    protected static ?string $pluralModelLabel = 'Leave Applications';
    protected static ?string $slug = 'leave-applications';

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->required()
                            ->minDate(now()->toDateString())
                            ->disabled(fn ($record) => $record !== null && $record->status !== 'Pending')
                            ->dehydrated()
                            ->rules([
                                fn ($record) => function (string $attribute, $value, \Closure $fail) use ($record) {
                                    if ($record === null) {
                                        $admissionId = Auth::user()->admission?->id;
                                        if ($admissionId) {
                                            $exists = \App\Models\LeaveApplication::where('admission_id', $admissionId)
                                                ->whereIn('status', ['Pending', 'Approved'])
                                                ->exists();
                                            if ($exists) {
                                                $fail('You already have an active or pending leave application.');
                                            }
                                        }
                                    }
                                }
                            ]),
                        Forms\Components\DatePicker::make('end_date')
                            ->required()
                            ->minDate(fn (callable $get) => $get('start_date') ?: now()->toDateString())
                            ->disabled(fn ($record) => $record !== null && $record->status !== 'Pending')
                            ->dehydrated(),
                        Forms\Components\Textarea::make('reason')
                            ->required()
                            ->rows(4)
                            ->disabled(fn ($record) => $record !== null && $record->status !== 'Pending')
                            ->dehydrated()
                            ->columnSpanFull(),
                        Forms\Components\Placeholder::make('status_display')
                            ->label('Status')
                            ->content(fn ($record) => $record?->status ?: 'Pending')
                            ->visible(fn ($record) => $record !== null),
                        Forms\Components\Placeholder::make('admin_remarks_display')
                            ->label('Admin Remarks')
                            ->content(fn ($record) => $record?->admin_remarks ?: 'No remarks yet.')
                            ->visible(fn ($record) => $record !== null && $record->admin_remarks !== null),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
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
                    ->label('Admin Remarks')
                    ->limit(30)
                    ->placeholder('N/A'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Actions\EditAction::make()
                    ->visible(fn (LeaveApplication $record) => $record->status === 'Pending'),
                Actions\DeleteAction::make()
                    ->visible(fn (LeaveApplication $record) => $record->status === 'Pending'),
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
            'index' => Pages\ListLeaveApplications::route('/'),
            'create' => Pages\CreateLeaveApplication::route('/create'),
            'edit' => Pages\EditLeaveApplication::route('/{record}/edit'),
        ];
    }
}
