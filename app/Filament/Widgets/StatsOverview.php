<?php

namespace App\Filament\Widgets;

use App\Models\Client;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Worker;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StatsOverview extends StatsOverviewWidget
{
    protected static bool $isLazy = true;

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Single query for project stats
        $projectStats = Project::query()
            ->selectRaw('COUNT(*) as total, SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as active')
            ->first();

        // Single query for worker stats
        $workerStats = Worker::query()
            ->selectRaw('COUNT(*) as total, SUM(CASE WHEN active = 1 THEN 1 ELSE 0 END) as active')
            ->first();

        // Batch the simple counts/sums
        $clientCount = Client::count();
        $totalIncome = Payment::sum('paid');
        $totalExpenses = Expense::sum('value');

        return [
            Stat::make(__('app.widgets.total_projects'), $projectStats->total)
                ->description(__('app.widgets.active_count') . $projectStats->active)
                ->icon('heroicon-o-building-office-2')
                ->chart([7, 3, 5, 8, 4, 6, 9])
                ->color('warning'),

            Stat::make(__('app.widgets.clients'), $clientCount)
                ->icon('heroicon-o-user-group')
                ->chart([3, 5, 4, 7, 6, 8, 5])
                ->color('success'),

            Stat::make(__('app.widgets.workers'), $workerStats->active)
                ->description(__('app.widgets.total_count') . $workerStats->total)
                ->icon('heroicon-o-wrench-screwdriver')
                ->chart([4, 6, 5, 3, 7, 5, 8])
                ->color('info'),

            Stat::make(__('app.widgets.total_income'), number_format($totalIncome, 2) . ' EGP')
                ->icon('heroicon-o-arrow-trending-up')
                ->chart([2, 4, 6, 5, 8, 7, 10])
                ->color('success'),

            Stat::make(__('app.widgets.total_expenses'), number_format($totalExpenses, 2) . ' EGP')
                ->icon('heroicon-o-arrow-trending-down')
                ->chart([5, 4, 6, 3, 5, 4, 3])
                ->color('danger'),
        ];
    }
}
