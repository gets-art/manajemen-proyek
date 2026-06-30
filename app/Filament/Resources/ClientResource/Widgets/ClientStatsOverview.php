<?php

namespace App\Filament\Resources\ClientResource\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ClientStatsOverview extends StatsOverviewWidget
{
    public $record;

    protected function getStats(): array
    {
        $client = $this->record;

        $stats = $client->projects()->selectRaw(
            'COUNT(*) as count, COALESCE(SUM(final_total), 0) as total_final, COALESCE(SUM(paid_total), 0) as total_paid, COALESCE(SUM(rest_total), 0) as total_rest'
        )->first();

        $projectsCount = $stats->count;
        $totalFinal = $stats->total_final;
        $totalPaid = $stats->total_paid;
        $totalRest = $stats->total_rest;

        return [
            Stat::make(__('app.columns.projects'), $projectsCount)
                ->description(__('app.widgets.total_projects_desc'))
                ->descriptionIcon('heroicon-m-building-office-2')
                ->chart([2, 3, 5, 4, 6, 3, 5])
                ->color('primary'),

            Stat::make(__('app.widgets.total_value'), number_format($totalFinal, 2) . ' IDR')
                ->description(__('app.widgets.combined_project_value'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart([5, 7, 6, 8, 9, 7, 10])
                ->color('info'),

            Stat::make(__('app.widgets.total_paid'), number_format($totalPaid, 2) . ' IDR')
                ->description(__('app.widgets.amount_received'))
                ->descriptionIcon('heroicon-m-check-circle')
                ->chart([3, 5, 4, 6, 7, 5, 8])
                ->color('success'),

            Stat::make(__('app.widgets.outstanding'), number_format($totalRest, 2) . ' IDR')
                ->description($totalRest > 0 ? __('app.widgets.needs_collection') : __('app.widgets.fully_settled'))
                ->descriptionIcon($totalRest > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-badge')
                ->chart([6, 5, 4, 3, 4, 3, 2])
                ->color($totalRest > 0 ? 'danger' : 'success'),
        ];
    }
}
