<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ActivityLogResource\Pages;
use Spatie\Activitylog\Models\Activity;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;

class ActivityLogResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static string | \UnitEnum | null $navigationGroup = 'User Management';
    protected static ?int $navigationSort = 4;
    protected static ?string $modelLabel = 'Audit Log';
    protected static ?string $pluralModelLabel = 'Audit Trails';

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
               Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('log_name')->disabled(),
                        Forms\Components\TextInput::make('description')->disabled(),
                        Forms\Components\TextInput::make('subject_type')->disabled(),
                        Forms\Components\TextInput::make('subject_id')->disabled(),
                        Forms\Components\TextInput::make('causer_type')->disabled(),
                        Forms\Components\TextInput::make('causer_id')->disabled(),
                        Forms\Components\KeyValue::make('properties')
                            ->disabled()
                            ->columnSpanFull(),
                        Forms\Components\DateTimePicker::make('created_at')->disabled(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Timestamp')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('log_name')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject_type')
                    ->label('Model')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('causer.name')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->placeholder('System'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('log_name')
                    ->options([
                        'default' => 'Default',
                    ]),
            ])
            ->actions([
                Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // Read-only, no bulk deletions
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageActivityLogs::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole('Super Admin') ?? false;
    }
}
