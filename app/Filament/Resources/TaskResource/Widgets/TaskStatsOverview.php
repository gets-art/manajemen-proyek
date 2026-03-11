<?php

namespace App\Filament\Resources\TaskResource\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class TaskStatsOverview extends StatsOverviewWidget
{
    public $record;

    protected function getStats(): array
    {
        $task = $this->record;

        $workersCount = $task->workers()->count();
        $workersPaid = DB::table('task_workers')
            ->where('task_id', $task->id)
            ->sum('paid') ?: 0;

        $purchasesCount = $task->purchaseTasks()->count();
        $purchasesTotal = $task->purchaseTasks()->sum('final_total') ?: 0;

        return [
            Stat::make(__('app.widgets.final_total'), number_format($task->final_total, 2) . ' EGP')
                ->description(__('app.widgets.project_value'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart([7, 3, 4, 5, 6, 3, 5])
                ->color('primary'),

            Stat::make(__('app.widgets.paid_total'), number_format($task->paid_total, 2) . ' EGP')
                ->description(__('app.widgets.amount_received'))
                ->descriptionIcon('heroicon-m-check-circle')
                ->chart([3, 5, 6, 7, 4, 5, 8])
                ->color('success'),

            Stat::make(__('app.widgets.remaining'), number_format($task->rest_total, 2) . ' EGP')
                ->description($task->rest_total > 0 ? __('app.widgets.outstanding_balance') : __('app.widgets.fully_paid'))
                ->descriptionIcon($task->rest_total > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-badge')
                ->chart([5, 4, 6, 3, 7, 5, 2])
                ->color($task->rest_total > 0 ? 'danger' : 'success'),

            Stat::make(__('app.widgets.workers'), $workersCount)
                ->description(number_format($workersPaid, 2) . ' EGP ' . __('app.columns.paid'))
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->chart([2, 4, 6, 5, 7, 4, 6])
                ->color('info'),

            Stat::make(__('app.widgets.purchases'), $purchasesCount)
                ->description(number_format($purchasesTotal, 2) . ' EGP ' . __('app.columns.total'))
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->chart([4, 6, 3, 7, 5, 4, 6])
                ->color('warning'),
        ];
    }
}
