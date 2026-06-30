<?php

namespace App\Filament\Resources\ProjectResource\Widgets;

use App\Models\Project;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AllProjectsStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        // Single query instead of 7
        $stats = Project::query()
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) as pending')
            ->selectRaw('SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as in_progress')
            ->selectRaw('SUM(CASE WHEN status = 2 THEN 1 ELSE 0 END) as completed')
            ->selectRaw('COALESCE(SUM(final_total), 0) as total_value')
            ->selectRaw('COALESCE(SUM(paid_total), 0) as total_paid')
            ->selectRaw('COALESCE(SUM(rest_total), 0) as total_rest')
            ->first();

        $totalProjects = $stats->total;
        $pendingProjects = $stats->pending;
        $inProgressProjects = $stats->in_progress;
        $completedProjects = $stats->completed;
        $totalValue = $stats->total_value;
        $totalPaid = $stats->total_paid;
        $totalRest = $stats->total_rest;

        return [
            Stat::make(__('app.widgets.total_projects'), $totalProjects)
                ->description($inProgressProjects . __('app.widgets.in_progress_count'))
                ->descriptionIcon('heroicon-m-building-office-2')
                ->chart([4, 6, 8, 5, 7, 9, 10])
                ->color('primary'),

            Stat::make(__('app.widgets.pending'), $pendingProjects)
                ->description(__('app.widgets.awaiting_start'))
                ->descriptionIcon('heroicon-m-clock')
                ->chart([2, 3, 2, 4, 3, 2, 3])
                ->color('warning'),

            Stat::make(__('app.widgets.completed'), $completedProjects)
                ->description(__('app.widgets.finished_projects'))
                ->descriptionIcon('heroicon-m-check-badge')
                ->chart([1, 2, 3, 4, 5, 6, 7])
                ->color('success'),

            Stat::make(__('app.widgets.total_value'), number_format($totalValue, 2) . ' IDR')
                ->description(__('app.widgets.all_projects_combined'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart([5, 7, 6, 8, 9, 7, 10])
                ->color('info'),

            Stat::make(__('app.widgets.total_paid'), number_format($totalPaid, 2) . ' IDR')
                ->description(number_format($totalRest, 2) . __('app.widgets.IDR_remaining'))
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->chart([3, 5, 4, 6, 7, 5, 8])
                ->color('success'),

            Stat::make(__('app.widgets.outstanding'), number_format($totalRest, 2) . ' IDR')
                ->description($totalRest > 0 ? __('app.widgets.needs_collection') : __('app.widgets.all_settled'))
                ->descriptionIcon($totalRest > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->chart([6, 5, 4, 3, 4, 3, 2])
                ->color($totalRest > 0 ? 'danger' : 'success'),
        ];
    }
}
