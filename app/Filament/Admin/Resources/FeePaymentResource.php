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
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $admission = Admission::find($state);
                                    if ($admission) {
                                        $totalPaid = $admission->payments()->sum('amount_paid');
                                        $remaining = max(0.00, $admission->final_fee - $totalPaid);
                                        $set('amount_paid', $remaining);
                                    }
                                } else {
                                    $set('amount_paid', null);
                                }
                            }),
                        Forms\Components\TextInput::make('amount_paid')
                            ->required()
                            ->numeric()
                            ->prefix('₹')
                            ->default(request()->query('amount_paid'))
                            ->rules([
                                fn (callable $get, $record) => function (string $attribute, $value, \Closure $fail) use ($get, $record) {
                                    $admissionId = $get('admission_id');
                                    if (!$admissionId) {
                                        return;
                                    }
                                    $admission = Admission::find($admissionId);
                                    if (!$admission) {
                                        return;
                                    }
                                    
                                    $totalPaid = $admission->payments()
                                        ->when($record, fn ($q) => $q->where('id', '!=', $record->id))
                                        ->sum('amount_paid');
                                    
                                    $remaining = $admission->final_fee - $totalPaid;
                                    
                                    if (floatval($value) <= 0) {
                                        $fail('The amount paid must be greater than zero.');
                                    }
                                    if (floatval($value) > $remaining) {
                                        $fail("The payment amount cannot exceed the remaining outstanding fee of ₹" . number_format($remaining, 2));
                                    }
                                }
                            ])
                            ->helperText(function (callable $get, ?FeePayment $record) {
                                $admissionId = $get('admission_id');
                                if (!$admissionId) {
                                    return 'Please select a student admission first.';
                                }
                                $admission = Admission::find($admissionId);
                                if (!$admission) {
                                    return '';
                                }
                                $totalPaid = $admission->payments()
                                    ->when($record, fn ($q) => $q->where('id', '!=', $record->id))
                                    ->sum('amount_paid');
                                $remaining = max(0.00, $admission->final_fee - $totalPaid);
                                return "Remaining outstanding balance: ₹" . number_format($remaining, 2);
                            }),
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
                Tables\Columns\TextColumn::make('admission.course.course_name')
                    ->label('Course')
                    ->searchable()
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
