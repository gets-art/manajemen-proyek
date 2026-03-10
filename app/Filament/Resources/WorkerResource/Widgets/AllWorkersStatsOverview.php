<?php

namespace App\Filament\Resources\WorkerResource\Widgets;

use App\Models\Worker;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AllWorkersStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        // Single query for worker counts
        $workerStats = Worker::query()
            ->selectRaw('COUNT(*) as total, SUM(CASE WHEN active = 1 THEN 1 ELSE 0 END) as active')
            ->first();
        $totalWorkers = $workerStats->total;
        $activeWorkers = $workerStats->active;

        $totalTasksPaid = DB::table('task_workers')->sum('paid');
        $totalPayments = DB::table('payments')
            ->where('paymentable_type', 'App\\Models\\Worker')
            ->sum('paid');
        $grandTotal = $totalTasksPaid + $totalPayments;

        return [
            Stat::make(__('app.widgets.total_workers'), $totalWorkers)
                ->description($activeWorkers . ' ' . __('app.widgets.active_label'))
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->chart([3, 4, 5, 4, 6, 5, 7])
                ->color('primary'),

            Stat::make(__('app.widgets.active_workers'), $activeWorkers)
                ->description(__('app.widgets.currently_active'))
                ->descriptionIcon('heroicon-m-check-circle')
                ->chart([4, 5, 6, 5, 7, 6, 8])
                ->color('success'),

            Stat::make(__('app.widgets.tasks_paid'), number_format($totalTasksPaid, 2) . ' EGP')
                ->description(__('app.widgets.total_task_payments'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart([5, 7, 6, 8, 9, 7, 10])
                ->color('info'),

            Stat::make(__('app.widgets.other_payments'), number_format($totalPayments, 2) . ' EGP')
                ->description(__('app.widgets.direct_payments'))
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->chart([4, 3, 5, 6, 4, 5, 7])
                ->color('warning'),

            Stat::make(__('app.widgets.grand_total'), number_format($grandTotal, 2) . ' EGP')
                ->description(__('app.widgets.all_worker_payments'))
                ->descriptionIcon('heroicon-m-calculator')
                ->chart([6, 8, 7, 9, 10, 8, 11])
                ->color('danger'),
        ];
    }
}
