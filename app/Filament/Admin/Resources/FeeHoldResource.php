<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\FeeHoldResource\Pages;
use App\Models\FeeHold;
use App\Models\User;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Actions;
use Filament\Tables\Table;

class FeeHoldResource extends Resource
{
    protected static ?string $model = FeeHold::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-pause';
    protected static string | \UnitEnum | null $navigationGroup = 'Fee Management';
    protected static ?int $navigationSort = 3;

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
                            ->default(request()->query('admission_id')),
                        Forms\Components\DatePicker::make('hold_from')
                            ->required()
                            ->default(now()),
                        Forms\Components\DatePicker::make('hold_to')
                            ->label('Hold To (Optional)'),
                        Forms\Components\Select::make('approved_by')
                            ->relationship('approver', 'name')
                            ->default(auth()->id())
                            ->required(),
                        Forms\Components\Textarea::make('reason')
                            ->required()
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
                Tables\Columns\TextColumn::make('hold_from')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('hold_to')
                    ->date()
                    ->placeholder('Indefinite')
                    ->sortable(),
                Tables\Columns\TextColumn::make('reason')
                    ->limit(50),
                Tables\Columns\TextColumn::make('approver.name')
                    ->label('Approved By'),
            ])
            ->filters([
                //
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageFeeHolds::route('/'),
        ];
    }
}
