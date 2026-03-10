<?php

namespace App\Filament\Resources\SupplierResource\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SupplierStatsOverview extends StatsOverviewWidget
{
    public $record;

    protected function getStats(): array
    {
        $supplier = $this->record;

        $purchaseStats = $supplier->purchaseTasks()->selectRaw(
            'COUNT(*) as count, COALESCE(SUM(total), 0) as total'
        )->first();
        $purchasesCount = $purchaseStats->count;
        $purchasesTotal = $purchaseStats->total;
        $paymentsTotal = $supplier->payments()->sum('paid') ?: 0;
        $balance = $purchasesTotal - $paymentsTotal;

        return [
            Stat::make(__('app.widgets.purchases'), $purchasesCount)
                ->description(__('app.widgets.total_purchase_orders'))
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->chart([2, 3, 5, 4, 6, 3, 5])
                ->color('primary'),

            Stat::make(__('app.widgets.purchases_total'), number_format($purchasesTotal, 2) . ' EGP')
                ->description(__('app.widgets.total_purchase_value'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart([5, 7, 6, 8, 9, 7, 10])
                ->color('info'),

            Stat::make(__('app.widgets.payments_total'), number_format($paymentsTotal, 2) . ' EGP')
                ->description(__('app.widgets.amount_paid_supplier'))
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->chart([3, 5, 4, 6, 7, 5, 8])
                ->color('success'),

            Stat::make(__('app.widgets.balance'), number_format($balance, 2) . ' EGP')
                ->description($balance > 0 ? __('app.widgets.outstanding_debt') : __('app.widgets.fully_paid'))
                ->descriptionIcon($balance > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-badge')
                ->chart([6, 5, 4, 3, 4, 3, 2])
                ->color($balance > 0 ? 'danger' : 'success'),
        ];
    }
}
