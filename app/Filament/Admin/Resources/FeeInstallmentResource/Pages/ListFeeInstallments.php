<?php

namespace App\Filament\Admin\Resources\FeeInstallmentResource\Pages;

use App\Filament\Admin\Resources\FeeInstallmentResource;
use App\Models\FeeInstallment;
use Filament\Actions;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListFeeInstallments extends ListRecords
{
    protected static string $resource = FeeInstallmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->badge(fn () => FeeInstallment::count()),
            'this_month' => Tab::make('Due This Month')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereBetween('due_date', [
                    now()->startOfMonth()->toDateString(),
                    now()->endOfMonth()->toDateString(),
                ])->where('status', '!=', 'Paid'))
                ->badge(fn () => FeeInstallment::whereBetween('due_date', [
                    now()->startOfMonth()->toDateString(),
                    now()->endOfMonth()->toDateString(),
                ])->where('status', '!=', 'Paid')->count())
                ->badgeColor('warning'),
            'overdue' => Tab::make('Overdue')
                ->modifyQueryUsing(fn (Builder $query) => $query->where(fn (Builder $q) => $q->where('status', 'Overdue')->orWhere(fn (Builder $q2) => $q2->where('due_date', '<', now()->toDateString())->where('status', '!=', 'Paid'))))
                ->badge(fn () => FeeInstallment::where(fn ($query) => $query->where('status', 'Overdue')->orWhere(fn ($q) => $q->where('due_date', '<', now()->toDateString())->where('status', '!=', 'Paid')))->count())
                ->badgeColor('danger'),
            'unpaid' => Tab::make('Unpaid')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', '!=', 'Paid'))
                ->badge(fn () => FeeInstallment::where('status', '!=', 'Paid')->count())
                ->badgeColor('info'),
            'paid' => Tab::make('Paid')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Paid'))
                ->badge(fn () => FeeInstallment::where('status', 'Paid')->count())
                ->badgeColor('success'),
        ];
    }
}
