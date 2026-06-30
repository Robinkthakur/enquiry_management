<?php

namespace App\Filament\Student\Resources;

use App\Filament\Student\Resources\ReceiptResource\Pages;
use App\Models\FeePayment;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ReceiptResource extends Resource
{
    protected static ?string $model = FeePayment::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'My Receipts';
    protected static ?string $modelLabel = 'Receipt';
    protected static ?string $pluralModelLabel = 'My Receipts';
    protected static ?string $slug = 'receipts';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                // Read-only, no form editing by student
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('receipt_no')
                    ->label('Receipt No')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('installment.enrollment.course.course_name')
                    ->label('Course')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount_paid')
                    ->label('Amount Paid')
                    ->money('INR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Payment Method')
                    ->sortable(),
                Tables\Columns\TextColumn::make('receipt_date')
                    ->label('Date')
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
            ])
            ->filters([
                //
            ])
            ->actions([
                // Download Receipt PDF Link
                Actions\Action::make('download')
                    ->label('PDF')
                    ->color('primary')
                    ->icon('heroicon-o-document-arrow-down')
                    ->visible(fn (FeePayment $record) => $record->status === 'Verified')
                    ->url(fn (FeePayment $record) => route('admin.payments.receipt', ['payment' => $record->id]))
                    ->openUrlInNewTab(),
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
            'index' => Pages\ListReceipts::route('/'),
        ];
    }
}
