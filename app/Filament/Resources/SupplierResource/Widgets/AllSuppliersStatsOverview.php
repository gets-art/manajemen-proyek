<?php

namespace App\Filament\Resources\SupplierResource\Widgets;

use App\Models\PurchaseTask;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AllSuppliersStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        // Single query for supplier counts
        $supplierStats = Supplier::query()
            ->selectRaw('COUNT(*) as total, SUM(CASE WHEN active = 1 THEN 1 ELSE 0 END) as active')
            ->first();
        $totalSuppliers = $supplierStats->total;
        $activeSuppliers = $supplierStats->active;

        $totalPurchases = PurchaseTask::sum('total');
        $totalPayments = DB::table('payments')
            ->where('paymentable_type', 'App\\Models\\Supplier')
            ->sum('paid');
        $balance = $totalPurchases - $totalPayments;

        return [
            Stat::make(__('app.widgets.total_suppliers'), $totalSuppliers)
                ->description($activeSuppliers . ' ' . __('app.widgets.active_label'))
                ->descriptionIcon('heroicon-m-truck')
                ->chart([3, 4, 5, 4, 6, 5, 7])
                ->color('primary'),

            Stat::make(__('app.widgets.active_suppliers'), $activeSuppliers)
                ->description(__('app.widgets.currently_active'))
                ->descriptionIcon('heroicon-m-check-circle')
                ->chart([4, 5, 6, 5, 7, 6, 8])
                ->color('success'),

            Stat::make(__('app.widgets.total_purchases'), number_format($totalPurchases, 2) . ' EGP')
                ->description(__('app.widgets.all_purchase_orders'))
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->chart([5, 7, 6, 8, 9, 7, 10])
                ->color('info'),

            Stat::make(__('app.widgets.total_paid'), number_format($totalPayments, 2) . ' EGP')
                ->description(__('app.widgets.payments_to_suppliers'))
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->chart([3, 5, 4, 6, 7, 5, 8])
                ->color('warning'),

            Stat::make(__('app.widgets.outstanding_balance'), number_format($balance, 2) . ' EGP')
                ->description($balance > 0 ? __('app.widgets.total_owed') : __('app.widgets.all_settled'))
                ->descriptionIcon($balance > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-badge')
                ->chart([6, 5, 4, 3, 4, 3, 2])
                ->color($balance > 0 ? 'danger' : 'success'),
        ];
    }
}
