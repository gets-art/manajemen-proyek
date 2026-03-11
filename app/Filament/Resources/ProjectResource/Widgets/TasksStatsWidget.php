<?php

namespace App\Filament\Resources\ProjectResource\Widgets;

use App\Models\Project;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TasksStatsWidget extends StatsOverviewWidget
{
    public ?int $projectId = null;

    protected function getStats(): array
    {
        $project = Project::find($this->projectId);

        if (! $project) {
            return [];
        }

        $stats = $project->tasks()
            ->selectRaw('COUNT(*) as tasks_count, COALESCE(SUM(final_total), 0) as total_final, COALESCE(SUM(paid_total), 0) as total_paid, COALESCE(SUM(rest_total), 0) as total_rest')
            ->first();

        return [
            Stat::make(__('app.widgets.tasks'), $stats->tasks_count)
                ->description(__('app.widgets.assigned_tasks'))
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->chart([2, 3, 5, 4, 6, 3, 5])
                ->color('primary'),

            Stat::make(__('app.widgets.final_total'), number_format($stats->total_final, 2) . ' EGP')
                ->description(__('app.widgets.project_value'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart([7, 3, 4, 5, 6, 3, 5])
                ->color('warning'),

            Stat::make(__('app.widgets.paid_total'), number_format($stats->total_paid, 2) . ' EGP')
                ->description(__('app.widgets.amount_received'))
                ->descriptionIcon('heroicon-m-check-circle')
                ->chart([3, 5, 6, 7, 4, 5, 8])
                ->color('success'),

            Stat::make(__('app.widgets.remaining'), number_format($stats->total_rest, 2) . ' EGP')
                ->description($stats->total_rest > 0 ? __('app.widgets.outstanding_balance') : __('app.widgets.fully_paid'))
                ->descriptionIcon($stats->total_rest > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-badge')
                ->chart([5, 4, 6, 3, 7, 5, 2])
                ->color($stats->total_rest > 0 ? 'danger' : 'success'),
        ];
    }
}
