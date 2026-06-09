<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\FeePaymentResource\Pages;
use App\Models\FeePayment;
use App\Models\FeeInstallment;
use App\Models\Admission;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Actions;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class FeePaymentResource extends Resource
{
    protected static ?string $model = FeePayment::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-credit-card';
    protected static string | \UnitEnum | null $navigationGroup = 'Fee Management';
    protected static ?int $navigationSort = 2;

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
                            ->afterStateUpdated(fn (callable $set) => $set('fee_installment_id', null)),
                        Forms\Components\Select::make('fee_installment_id')
                            ->label('Select Installment')
                            ->required()
                            ->options(function (callable $get) {
                                $admissionId = $get('admission_id');
                                if (!$admissionId) {
                                    return [];
                                }
                                return FeeInstallment::where('admission_id', $admissionId)
                                    ->where('status', '!=', 'Paid')
                                    ->get()
                                    ->mapWithKeys(fn ($inst) => [
                                        $inst->id => "Inst #{$inst->installment_no} (Due: {$inst->due_date->format('Y-m-d')} - Due Amt: ₹{$inst->due_amount})"
                                    ])
                                    ->toArray();
                            })
                            ->default(request()->query('fee_installment_id'))
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                     $inst = FeeInstallment::find($state);
                                     if ($inst) {
                                         $set('amount_paid', $inst->due_amount);
                                     }
                                 }
                            }),
                        Forms\Components\TextInput::make('amount_paid')
                            ->required()
                            ->numeric()
                            ->prefix('₹')
                            ->default(request()->query('amount_paid')),
                        Forms\Components\Select::make('payment_method')
                            ->required()
                            ->options([
                                'Cash' => 'Cash',
                                'UPI' => 'UPI',
                                'Card' => 'Card',
                                'Bank Transfer' => 'Bank Transfer',
                            ])
                            ->default('UPI'),
                        Forms\Components\TextInput::make('transaction_reference')
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('receipt_date')
                            ->required()
                            ->default(now()),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('receipt_no')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('admission.student_name')
                    ->label('Student')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('installment.installment_no')
                    ->label('Inst No')
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount_paid')
                    ->money('INR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->sortable(),
                Tables\Columns\TextColumn::make('receipt_date')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('payment_method')
                    ->options([
                        'Cash' => 'Cash',
                        'UPI' => 'UPI',
                        'Card' => 'Card',
                        'Bank Transfer' => 'Bank Transfer',
                    ]),
                Tables\Filters\SelectFilter::make('admission_id')
                    ->relationship('admission', 'student_name')
                    ->searchable(),
            ])
            ->actions([
                Actions\ActionGroup::make([
                    Actions\EditAction::make(),
                    
                    // Download Receipt PDF
                    Actions\Action::make('download_receipt')
                        ->label('PDF')
                        ->color('primary')
                        ->icon('heroicon-o-document-arrow-down')
                        ->url(fn (FeePayment $record) => "/admin/payments/{$record->id}/receipt")
                        ->openUrlInNewTab(),

                    // Email Receipt action
                    Actions\Action::make('email_receipt')
                        ->label('Email')
                        ->color('info')
                        ->icon('heroicon-o-envelope')
                        ->requiresConfirmation()
                        ->action(function (FeePayment $record) {
                            try {
                                $student = $record->admission;
                                if (!$student->email) {
                                    Notification::make()->title('Student has no email address.')->danger()->send();
                                    return;
                                }
                                
                                // Dispatch Notification
                                $student->notify(new \App\Notifications\FeeReceiptNotification($record));
                                
                                Notification::make()
                                    ->title('Receipt Emailed')
                                    ->body("The receipt has been sent to {$student->email} successfully.")
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Email Failed')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),
                ]),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFeePayments::route('/'),
            'create' => Pages\CreateFeePayment::route('/create'),
            'edit' => Pages\EditFeePayment::route('/{record}/edit'),
        ];
    }
}
