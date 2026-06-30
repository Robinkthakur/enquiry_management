<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\FeePaymentResource\Pages;
use App\Models\FeePayment;
use App\Models\Admission;
use App\Models\AdmissionCourse;
use App\Models\FeeInstallment;
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
                                $set('admission_course_id', null);
                                $set('fee_installment_id', null);
                                $set('amount_paid', null);
                            }),
                        Forms\Components\Select::make('admission_course_id')
                            ->label('Course')
                            ->options(function (callable $get) {
                                $admissionId = $get('admission_id');
                                if (!$admissionId) {
                                    return [];
                                }
                                return AdmissionCourse::where('admission_id', $admissionId)
                                    ->with('course')
                                    ->get()
                                    ->filter(function ($enrollment) {
                                        $paid = $enrollment->installments()->sum('paid_amount');
                                        return ($enrollment->final_fee - $paid) > 0;
                                    })
                                    ->mapWithKeys(function ($enrollment) {
                                        $paid = $enrollment->installments()->sum('paid_amount');
                                        $pending = max(0.00, $enrollment->final_fee - $paid);
                                        $label = "{$enrollment->course->course_name} (Pending Dues: ₹" . number_format($pending, 2) . ")";
                                        return [$enrollment->id => $label];
                                    })->toArray();
                            })
                            ->required()
                            ->reactive()
                            ->default(function () {
                                $instId = request()->query('fee_installment_id');
                                if ($instId) {
                                    $inst = FeeInstallment::find($instId);
                                    return $inst ? $inst->admission_course_id : null;
                                }
                                return null;
                            })
                            ->afterStateHydrated(function ($component, $state, callable $get) {
                                $instId = $get('fee_installment_id');
                                if ($instId) {
                                    $inst = FeeInstallment::find($instId);
                                    if ($inst) {
                                        $component->state($inst->admission_course_id);
                                    }
                                }
                            })
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $enrollment = AdmissionCourse::find($state);
                                    if ($enrollment) {
                                        $paid = $enrollment->installments()->sum('paid_amount');
                                        $pending = max(0.00, $enrollment->final_fee - $paid);
                                        $set('amount_paid', $pending);

                                        // Find corresponding oldest pending installment to assign to the payment record
                                        $oldestPending = FeeInstallment::where('admission_course_id', $state)
                                            ->where('due_amount', '>', 0)
                                            ->orderBy('due_date', 'asc')
                                            ->orderBy('installment_no', 'asc')
                                            ->first();
                                        if ($oldestPending) {
                                            $set('fee_installment_id', $oldestPending->id);
                                        } else {
                                            $firstInst = FeeInstallment::where('admission_course_id', $state)
                                                ->orderBy('installment_no', 'asc')
                                                ->first();
                                            $set('fee_installment_id', $firstInst ? $firstInst->id : null);
                                        }
                                    }
                                } else {
                                    $set('fee_installment_id', null);
                                    $set('amount_paid', null);
                                }
                            }),
                        Forms\Components\Hidden::make('fee_installment_id')
                            ->default(request()->query('fee_installment_id')),
                        Forms\Components\TextInput::make('amount_paid')
                            ->required()
                            ->numeric()
                            ->prefix('₹')
                            ->default(request()->query('amount_paid'))
                            ->rules([
                                fn (callable $get, $record) => function (string $attribute, $value, \Closure $fail) use ($get, $record) {
                                    $enrollmentId = $get('admission_course_id');
                                    if (!$enrollmentId) {
                                        return;
                                    }
                                    $enrollment = AdmissionCourse::find($enrollmentId);
                                    if (!$enrollment) {
                                        return;
                                    }
                                    
                                    // Total paid for this course enrollment excluding current record
                                    $totalPaid = FeePayment::whereHas('installment', function ($q) use ($enrollmentId) {
                                            $q->where('admission_course_id', $enrollmentId);
                                        })
                                        ->when($record, fn ($q) => $q->where('id', '!=', $record->id))
                                        ->sum('amount_paid');
                                    
                                    $remaining = $enrollment->final_fee - $totalPaid;
                                    
                                    if (floatval($value) <= 0) {
                                        $fail('The amount paid must be greater than zero.');
                                    }
                                    if (floatval($value) > $remaining + 0.01) {
                                        $fail("The payment amount cannot exceed the remaining outstanding fee of ₹" . number_format($remaining, 2));
                                    }
                                }
                            ])
                            ->helperText(function (callable $get, ?FeePayment $record) {
                                $enrollmentId = $get('admission_course_id');
                                if (!$enrollmentId) {
                                    return 'Please select a course first.';
                                }
                                $enrollment = AdmissionCourse::find($enrollmentId);
                                if (!$enrollment) {
                                    return '';
                                }
                                $totalPaid = FeePayment::whereHas('installment', function ($q) use ($enrollmentId) {
                                        $q->where('admission_course_id', $enrollmentId);
                                    })
                                    ->when($record, fn ($q) => $q->where('id', '!=', $record->id))
                                    ->sum('amount_paid');
                                $remaining = max(0.00, $enrollment->final_fee - $totalPaid);
                                return "Remaining outstanding balance for this course: ₹" . number_format($remaining, 2);
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
                        Forms\Components\Select::make('status')
                            ->options([
                                'Pending' => 'Pending',
                                'Verified' => 'Verified',
                                'Rejected' => 'Rejected',
                            ])
                            ->required()
                            ->default('Verified'),
                        Forms\Components\FileUpload::make('screenshot')
                            ->label('Payment Screenshot')
                            ->image()
                            ->directory('payment_screenshots')
                            ->visible(fn ($record) => $record !== null && $record->screenshot !== null)
                            ->disabled()
                            ->dehydrated(false),
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
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Verified' => 'success',
                        'Pending' => 'warning',
                        'Rejected' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\ImageColumn::make('screenshot')
                    ->label('Screenshot')
                    ->disk('local')
                    ->url(fn (FeePayment $record) => $record->screenshot ? route('admin.payment-screenshot', ['path' => $record->screenshot]) : null)
                    ->openUrlInNewTab()
                    ->square()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Pending' => 'Pending',
                        'Verified' => 'Verified',
                        'Rejected' => 'Rejected',
                    ]),
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
                    
                    Actions\Action::make('verify_payment')
                        ->label('Verify')
                        ->color('success')
                        ->icon('heroicon-o-check-circle')
                        ->visible(fn (FeePayment $record) => $record->status === 'Pending')
                        ->requiresConfirmation()
                        ->action(function (FeePayment $record) {
                            $record->status = 'Verified';
                            $record->save();

                            Notification::make()
                                ->title('Payment Verified')
                                ->body("Payment of ₹" . number_format($record->amount_paid, 2) . " has been verified.")
                                ->success()
                                ->send();
                        }),

                    Actions\Action::make('reject_payment')
                        ->label('Reject')
                        ->color('danger')
                        ->icon('heroicon-o-x-circle')
                        ->visible(fn (FeePayment $record) => $record->status === 'Pending')
                        ->requiresConfirmation()
                        ->action(function (FeePayment $record) {
                            $record->status = 'Rejected';
                            $record->save();

                            Notification::make()
                                ->title('Payment Rejected')
                                ->body("Payment of ₹" . number_format($record->amount_paid, 2) . " has been rejected.")
                                ->danger()
                                ->send();
                        }),
                    
                    // Download Receipt PDF
                    Actions\Action::make('download_receipt')
                        ->label('PDF')
                        ->color('primary')
                        ->icon('heroicon-o-document-arrow-down')
                        ->visible(fn (FeePayment $record) => $record->status === 'Verified')
                        ->url(fn (FeePayment $record) => "/admin/payments/{$record->id}/receipt")
                        ->openUrlInNewTab(),

                    // Email Receipt action
                    Actions\Action::make('email_receipt')
                        ->label('Email')
                        ->color('info')
                        ->icon('heroicon-o-envelope')
                        ->visible(fn (FeePayment $record) => $record->status === 'Verified')
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
            ])
            ->defaultSort('created_at', 'desc');
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
