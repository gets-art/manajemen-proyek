<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StatsOverview extends StatsOverviewWidget
{
    protected static bool $isLazy = true;

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $stats = DB::selectOne("
            SELECT
                (SELECT COUNT(*) FROM projects WHERE deleted_at IS NULL) as total_projects,
                (SELECT COUNT(*) FROM projects WHERE status = 1 AND deleted_at IS NULL) as active_projects,
                (SELECT COUNT(*) FROM clients WHERE deleted_at IS NULL) as total_clients,
                (SELECT COUNT(*) FROM workers WHERE active = 1 AND deleted_at IS NULL) as active_workers,
                (SELECT COUNT(*) FROM workers WHERE deleted_at IS NULL) as total_workers,
                (SELECT COALESCE(SUM(paid), 0) FROM payments WHERE deleted_at IS NULL AND paymentable_type = ?) as project_income,
                (SELECT COALESCE(SUM(paid), 0) FROM payments WHERE deleted_at IS NULL AND paymentable_type != ?) as other_payments,
                (SELECT COALESCE(SUM(value), 0) FROM expenses WHERE deleted_at IS NULL) as system_expenses,
                (SELECT COUNT(*) FROM tasks WHERE deleted_at IS NULL) as total_tasks
        ", [\App\Models\Project::class, \App\Models\Project::class]);

        $totalExpenses = $stats->other_payments + $stats->system_expenses;
        $netProfit = $stats->project_income - $totalExpenses;

        return [
            Stat::make(__('app.widgets.total_projects'), $stats->total_projects)
                ->description(__('app.widgets.active_count') . $stats->active_projects)
                ->descriptionIcon('heroicon-m-building-office-2')
                ->chart([4, 6, 8, 5, 7, 9, 10])
                ->color('warning'),

            Stat::make(__('app.widgets.clients'), $stats->total_clients)
                ->description(__('app.widgets.registered_clients'))
                ->descriptionIcon('heroicon-m-user-group')
                ->chart([3, 5, 4, 7, 6, 8, 9])
                ->color('success'),

            Stat::make(__('app.widgets.workers'), $stats->active_workers)
                ->description(__('app.widgets.total_count') . $stats->total_workers)
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->chart([2, 4, 3, 5, 4, 6, 5])
                ->color('info'),

            Stat::make(__('app.widgets.total_tasks'), $stats->total_tasks)
                ->description(__('app.widgets.assigned_tasks'))
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->chart([5, 3, 7, 4, 6, 8, 5])
                ->color('primary'),

            Stat::make(__('app.widgets.total_income'), number_format($stats->project_income, 2) . ' IDR')
                ->description(__('app.widgets.all_payments_combined'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([3, 5, 6, 7, 4, 8, 10])
                ->color('success'),

            Stat::make(__('app.widgets.total_expenses'), number_format($totalExpenses, 2) . ' IDR')
                ->description(__('app.widgets.total_expenses_desc'))
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->chart([6, 5, 4, 7, 5, 3, 4])
                ->color('danger'),

            Stat::make(__('app.widgets.net_profit'), number_format($netProfit, 2) . ' IDR')
                ->description($netProfit >= 0 ? __('app.widgets.all_settled') : __('app.widgets.outstanding_balance'))
                ->descriptionIcon($netProfit >= 0 ? 'heroicon-m-check-circle' : 'heroicon-m-exclamation-triangle')
                ->chart([4, 6, 5, 8, 7, 9, 11])
                ->color($netProfit >= 0 ? 'success' : 'danger'),
        ];
    }
}
