<?php

namespace App\Filament\Resources\WorkerResource\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class WorkerStatsOverview extends StatsOverviewWidget
{
    public $record;

    protected function getStats(): array
    {
        $worker = $this->record;

        $pivot = DB::table('task_workers')
            ->where('worker_id', $worker->id)
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(paid), 0) as total_paid')
            ->first();
        $tasksCount = $pivot->count;
        $tasksPaid = $pivot->total_paid;
        $paymentsTotal = $worker->payments()->sum('paid') ?: 0;
        $grandTotal = $tasksPaid + $paymentsTotal;

        return [
            Stat::make(__('app.widgets.tasks'), $tasksCount)
                ->description(__('app.widgets.assigned_tasks'))
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->chart([2, 3, 5, 4, 6, 3, 5])
                ->color('primary'),

            Stat::make(__('app.widgets.tasks_paid'), number_format($tasksPaid, 2) . ' EGP')
                ->description(__('app.widgets.from_task_assignments'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart([3, 5, 4, 6, 7, 5, 8])
                ->color('info'),

            Stat::make(__('app.widgets.other_payments'), number_format($paymentsTotal, 2) . ' EGP')
                ->description(__('app.widgets.direct_payments'))
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->chart([4, 3, 5, 6, 4, 5, 7])
                ->color('warning'),

            Stat::make(__('app.widgets.grand_total'), number_format($grandTotal, 2) . ' EGP')
                ->description(__('app.widgets.all_payments_combined'))
                ->descriptionIcon('heroicon-m-calculator')
                ->chart([5, 7, 6, 8, 9, 7, 10])
                ->color('success'),
        ];
    }
}
