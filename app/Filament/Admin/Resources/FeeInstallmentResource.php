<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\FeeInstallmentResource\Pages;
use App\Models\FeeInstallment;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Actions;
use Filament\Tables\Table;

class FeeInstallmentResource extends Resource
{
    protected static ?string $model = FeeInstallment::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-currency-dollar';
    protected static string | \UnitEnum | null $navigationGroup = 'Fee Management';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Forms\Components\Select::make('admission_id')
                            ->relationship('admission', 'student_name')
                            ->disabled()
                            ->required(),
                        Forms\Components\TextInput::make('installment_no')
                            ->numeric()
                            ->disabled()
                            ->required(),
                        Forms\Components\DatePicker::make('due_date')
                            ->required(),
                        Forms\Components\TextInput::make('amount')
                            ->numeric()
                            ->prefix('₹')
                            ->required(),
                        Forms\Components\TextInput::make('paid_amount')
                            ->numeric()
                            ->prefix('₹')
                            ->disabled(),
                        Forms\Components\TextInput::make('due_amount')
                            ->numeric()
                            ->prefix('₹')
                            ->disabled(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'Pending' => 'Pending',
                                'Paid' => 'Paid',
                                'Partial' => 'Partial',
                                'Overdue' => 'Overdue',
                                'Hold' => 'Hold',
                             ])
                            ->required(),
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
                Tables\Columns\TextColumn::make('admission.roll_no')
                    ->label('Roll No')
                    ->searchable(),
                Tables\Columns\TextColumn::make('installment_no')
                    ->label('Inst No')
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->money('INR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('paid_amount')
                    ->money('INR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_amount')
                    ->money('INR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Paid' => 'success',
                        'Pending' => 'warning',
                        'Partial' => 'info',
                        'Overdue' => 'danger',
                        'Hold' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Pending' => 'Pending',
                        'Paid' => 'Paid',
                        'Partial' => 'Partial',
                        'Overdue' => 'Overdue',
                        'Hold' => 'Hold',
                    ]),
            ])
            ->actions([
                Actions\ActionGroup::make([
                    Actions\EditAction::make(),
                    
                    // Record Payment Action
                    Actions\Action::make('record_payment')
                        ->label('Pay')
                        ->color('success')
                        ->icon('heroicon-o-credit-card')
                        ->visible(fn (FeeInstallment $record) => $record->status !== 'Paid')
                        ->url(fn (FeeInstallment $record) => route('filament.admin.resources.fee-payments.create', [
                            'admission_id' => $record->admission_id,
                            'fee_installment_id' => $record->id,
                            'amount_paid' => $record->due_amount
                        ])),
                ]),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFeeInstallments::route('/'),
            'create' => Pages\CreateFeeInstallment::route('/create'),
            'edit' => Pages\EditFeeInstallment::route('/{record}/edit'),
        ];
    }
}
