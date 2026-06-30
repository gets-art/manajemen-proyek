<?php

namespace App\Filament\Resources\WorkerResource\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class WorkerTasksStatsWidget extends StatsOverviewWidget
{
    public ?int $workerId = null;

    protected function getStats(): array
    {
        if (! $this->workerId) {
            return [];
        }

        $stats = DB::selectOne("
            SELECT
                COUNT(*) as tasks_count,
                COALESCE(SUM(t.final_total), 0) as total_final,
                COALESCE(SUM(t.paid_total), 0) as total_paid,
                COALESCE(SUM(t.rest_total), 0) as total_rest,
                COALESCE(SUM(tw.paid), 0) as total_worker_paid
            FROM tasks t
            INNER JOIN task_workers tw ON t.id = tw.task_id
            WHERE tw.worker_id = ? AND t.deleted_at IS NULL
        ", [$this->workerId]);

        return [
            Stat::make(__('app.widgets.tasks'), $stats->tasks_count)
                ->description(__('app.widgets.assigned_tasks'))
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->chart([2, 3, 5, 4, 6, 3, 5])
                ->color('primary'),

            Stat::make(__('app.widgets.final_total'), number_format($stats->total_final, 2) . ' IDR')
                ->description(__('app.widgets.project_value'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart([7, 3, 4, 5, 6, 3, 5])
                ->color('warning'),

            Stat::make(__('app.widgets.paid_total'), number_format($stats->total_paid, 2) . ' IDR')
                ->description(__('app.widgets.amount_received'))
                ->descriptionIcon('heroicon-m-check-circle')
                ->chart([3, 5, 6, 7, 4, 5, 8])
                ->color('success'),

            Stat::make(__('app.widgets.remaining'), number_format($stats->total_rest, 2) . ' IDR')
                ->description($stats->total_rest > 0 ? __('app.widgets.outstanding_balance') : __('app.widgets.fully_paid'))
                ->descriptionIcon($stats->total_rest > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-badge')
                ->chart([5, 4, 6, 3, 7, 5, 2])
                ->color($stats->total_rest > 0 ? 'danger' : 'success'),

            Stat::make(__('app.fields.paid_amount'), number_format($stats->total_worker_paid, 2) . ' IDR')
                ->description(__('app.widgets.total_payments_received'))
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->chart([2, 4, 6, 5, 7, 4, 6])
                ->color('info'),
        ];
    }
}
