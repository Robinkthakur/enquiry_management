<?php

namespace App\Filament\Admin\Resources\DueFeeResource\Pages;

use App\Filament\Admin\Resources\DueFeeResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListDueFees extends ListRecords
{
    protected static string $resource = DueFeeResource::class;

    protected function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('due_amount', '>', 0);
    }

    public function getTabs(): array
    {
        $today = now()->toDateString();
        $startOfWeek = now()->startOfWeek()->toDateString();
        $endOfWeek = now()->endOfWeek()->toDateString();
        $startOfMonth = now()->startOfMonth()->toDateString();
        $endOfMonth = now()->endOfMonth()->toDateString();

        return [
            'overdue' => Tab::make('Overdue')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('due_date', '<', $today)),
            'this_week' => Tab::make('This Week')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereBetween('due_date', [$startOfWeek, $endOfWeek])),
            'this_month' => Tab::make('This Month')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereBetween('due_date', [$startOfMonth, $endOfMonth])),
            'all' => Tab::make('All Due'),
        ];
    }
}
