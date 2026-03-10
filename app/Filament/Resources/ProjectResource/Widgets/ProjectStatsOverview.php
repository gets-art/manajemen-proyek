<?php

namespace App\Filament\Resources\ProjectResource\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProjectStatsOverview extends StatsOverviewWidget
{
    public $record;

    protected function getStats(): array
    {
        $project = $this->record;

        $tasksCount = $project->tasks()->count();
        $expensesTotal = $project->expenses()->sum('value') ?: 0;
        $paymentsTotal = $project->payments()->sum('paid') ?: 0;

        return [
            Stat::make(__('app.widgets.final_total'), number_format($project->final_total, 2) . ' EGP')
                ->description(__('app.widgets.project_value'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart([7, 3, 4, 5, 6, 3, 5])
                ->color('primary'),

            Stat::make(__('app.widgets.paid_total'), number_format($project->paid_total, 2) . ' EGP')
                ->description(__('app.widgets.amount_received'))
                ->descriptionIcon('heroicon-m-check-circle')
                ->chart([3, 5, 6, 7, 4, 5, 8])
                ->color('success'),

            Stat::make(__('app.widgets.remaining'), number_format($project->rest_total, 2) . ' EGP')
                ->description($project->rest_total > 0 ? __('app.widgets.outstanding_balance') : __('app.widgets.fully_paid'))
                ->descriptionIcon($project->rest_total > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-badge')
                ->chart([5, 4, 6, 3, 7, 5, 2])
                ->color($project->rest_total > 0 ? 'danger' : 'success'),

            Stat::make(__('app.widgets.payments'), number_format($paymentsTotal, 2) . ' EGP')
                ->description(__('app.widgets.total_payments_received'))
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->chart([2, 4, 6, 5, 7, 4, 6])
                ->color('info'),

            Stat::make(__('app.widgets.expenses'), number_format($expensesTotal, 2) . ' EGP')
                ->description(__('app.widgets.total_expenses_desc'))
                ->descriptionIcon('heroicon-m-receipt-percent')
                ->chart([4, 6, 3, 7, 5, 4, 6])
                ->color('warning'),

            Stat::make(__('app.widgets.tasks'), $tasksCount)
                ->description(__('app.widgets.assigned_tasks'))
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->chart([2, 3, 5, 4, 6, 3, 5])
                ->color('gray'),
        ];
    }
}
