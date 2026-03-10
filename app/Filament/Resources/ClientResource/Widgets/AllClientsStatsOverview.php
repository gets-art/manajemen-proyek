<?php

namespace App\Filament\Resources\ClientResource\Widgets;

use App\Models\Client;
use App\Models\Project;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AllClientsStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalClients = Client::count();

        // Single query instead of 4
        $projectStats = Project::query()
            ->selectRaw('COUNT(*) as total, COALESCE(SUM(final_total), 0) as total_value, COALESCE(SUM(paid_total), 0) as total_paid, COALESCE(SUM(rest_total), 0) as total_rest')
            ->first();

        $totalProjects = $projectStats->total;
        $totalValue = $projectStats->total_value;
        $totalPaid = $projectStats->total_paid;
        $totalRest = $projectStats->total_rest;

        return [
            Stat::make(__('app.widgets.total_clients'), $totalClients)
                ->description(__('app.widgets.registered_clients'))
                ->descriptionIcon('heroicon-m-user-group')
                ->chart([3, 5, 4, 6, 7, 8, 9])
                ->color('primary'),

            Stat::make(__('app.widgets.total_projects'), $totalProjects)
                ->description(__('app.widgets.across_all_clients'))
                ->descriptionIcon('heroicon-m-building-office-2')
                ->chart([4, 6, 5, 7, 8, 6, 9])
                ->color('info'),

            Stat::make(__('app.widgets.total_value'), number_format($totalValue, 2) . ' EGP')
                ->description(__('app.widgets.all_projects_combined'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart([5, 7, 6, 8, 9, 7, 10])
                ->color('warning'),

            Stat::make(__('app.widgets.total_paid'), number_format($totalPaid, 2) . ' EGP')
                ->description(__('app.widgets.amount_collected'))
                ->descriptionIcon('heroicon-m-check-circle')
                ->chart([3, 5, 4, 6, 7, 5, 8])
                ->color('success'),

            Stat::make(__('app.widgets.outstanding'), number_format($totalRest, 2) . ' EGP')
                ->description($totalRest > 0 ? __('app.widgets.needs_collection') : __('app.widgets.all_settled'))
                ->descriptionIcon($totalRest > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-badge')
                ->chart([6, 5, 4, 3, 4, 3, 2])
                ->color($totalRest > 0 ? 'danger' : 'success'),
        ];
    }
}
