<?php

namespace App\Filament\Resources\SupplierResource\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class PurchaseTasksStatsWidget extends StatsOverviewWidget
{
    public ?int $supplierId = null;

    protected function getStats(): array
    {
        if (! $this->supplierId) {
            return [];
        }

        $stats = DB::selectOne("
            SELECT
                COUNT(*) as purchases_count,
                COALESCE(SUM(total), 0) as total_amount,
                COALESCE(SUM(discount), 0) as total_discount,
                COALESCE(SUM(final_total), 0) as total_final,
                COALESCE(SUM(quantity), 0) as total_quantity
            FROM purchase_tasks
            WHERE supplier_id = ?
        ", [$this->supplierId]);

        return [
            Stat::make(__('app.sections.purchase_details'), $stats->purchases_count)
                ->description(__('app.widgets.total_count') . $stats->total_quantity . ' items')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->chart([2, 3, 5, 4, 6, 3, 5])
                ->color('primary'),

            Stat::make(__('app.widgets.total_value'), number_format($stats->total_amount, 2) . ' EGP')
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart([7, 3, 4, 5, 6, 3, 5])
                ->color('warning'),

            Stat::make(__('app.fields.discount'), number_format($stats->total_discount, 2) . ' EGP')
                ->description(__('app.widgets.total_purchase_value'))
                ->descriptionIcon('heroicon-m-receipt-percent')
                ->chart([3, 5, 6, 7, 4, 5, 8])
                ->color('info'),

            Stat::make(__('app.widgets.final_total'), number_format($stats->total_final, 2) . ' EGP')
                ->descriptionIcon('heroicon-m-check-circle')
                ->chart([5, 4, 6, 3, 7, 5, 2])
                ->color('success'),
        ];
    }
}
