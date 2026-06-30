<?php

namespace App\Filament\Student\Resources;

use App\Filament\Student\Resources\FeeInstallmentResource\Pages;
use App\Models\FeeInstallment;
use App\Models\FeePayment;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class FeeInstallmentResource extends Resource
{
    protected static ?string $model = FeeInstallment::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'Fee Installments';
    protected static ?string $modelLabel = 'Fee Installment';
    protected static ?string $pluralModelLabel = 'Fee Installments';
    protected static ?string $slug = 'fee-installments';
    protected static ?int $navigationSort = 4;

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                // Read-only on pages
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('installment_no')
                    ->label('Inst No')
                    ->sortable(),
                Tables\Columns\TextColumn::make('enrollment.course.course_name')
                    ->label('Course')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('INR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('paid_amount')
                    ->label('Paid')
                    ->money('INR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_amount')
                    ->label('Due')
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
                Actions\Action::make('pay_qr')
                    ->label('Pay via QR')
                    ->icon('heroicon-o-qr-code')
                    ->color('success')
                    ->visible(fn (FeeInstallment $record) => $record->status !== 'Paid')
                    ->form([
                        Forms\Components\Placeholder::make('qr_code_placeholder')
                            ->label('')
                            ->content(new \Illuminate\Support\HtmlString('
                                <div class="flex flex-col items-center justify-center p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700">
                                    <img src="/payment_qr_code.png" class="w-48 h-48 object-contain rounded-lg shadow-md border" alt="QR Code" />
                                    <p class="mt-3 text-xs text-gray-500 text-center font-medium">Scan this QR code using any UPI app to make the payment.</p>
                                </div>
                            ')),
                        Forms\Components\TextInput::make('amount_paid')
                            ->label('Amount Paid')
                            ->numeric()
                            ->required()
                            ->default(fn (FeeInstallment $record) => $record->due_amount)
                            ->prefix('₹'),
                        Forms\Components\Select::make('payment_method')
                            ->label('Payment Method')
                            ->options([
                                'UPI/QR Code' => 'UPI/QR Code',
                                'Bank Transfer' => 'Bank Transfer',
                            ])
                            ->required()
                            ->default('UPI/QR Code'),
                        Forms\Components\TextInput::make('transaction_reference')
                            ->label('Transaction ID / UTR No')
                            ->required()
                            ->rules(['unique:fee_payments,transaction_reference'])
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('receipt_date')
                            ->label('Payment Date')
                            ->required()
                            ->default(now()),
                        Forms\Components\FileUpload::make('screenshot')
                            ->label('Upload Payment Screenshot')
                            ->image()
                            ->directory('payment_screenshots')
                            ->required(),
                    ])
                    ->action(function (FeeInstallment $record, array $data): void {
                        $payment = new FeePayment();
                        $payment->admission_id = $record->admission_id;
                        $payment->fee_installment_id = $record->id;
                        $payment->amount_paid = $data['amount_paid'];
                        $payment->payment_method = $data['payment_method'];
                        $payment->transaction_reference = $data['transaction_reference'];
                        $payment->receipt_date = $data['receipt_date'];
                        $payment->screenshot = $data['screenshot'];
                        $payment->status = 'Pending';
                        $payment->save();

                        \Filament\Notifications\Notification::make()
                            ->title('Payment proof submitted successfully!')
                            ->body('Your payment is pending review by the admin.')
                            ->success()
                            ->send();
                    })
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
            'index' => Pages\ListFeeInstallments::route('/'),
        ];
    }
}
