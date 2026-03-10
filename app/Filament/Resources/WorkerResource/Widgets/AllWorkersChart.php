<?php

namespace App\Filament\Resources\WorkerResource\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AllWorkersChart extends ChartWidget
{
    public function getHeading(): ?string
    {
        return __('app.widgets.worker_payments_over_time');
    }

    protected ?string $maxHeight = '300px';

    protected ?string $pollingInterval = null;

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $taskPayments = DB::table('task_workers')
            ->selectRaw('MONTH(created_at) as month, SUM(paid) as total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month')
            ->toArray();

        $otherPayments = DB::table('payments')
            ->where('paymentable_type', 'App\\Models\\Worker')
            ->selectRaw('MONTH(created_at) as month, SUM(paid) as total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month')
            ->toArray();

        $months = [];
        $tasksData = [];
        $paymentsData = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[] = Carbon::create()->month($i)->format('M');
            $tasksData[] = $taskPayments[$i] ?? 0;
            $paymentsData[] = $otherPayments[$i] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => __('app.widgets.tasks_paid_egp'),
                    'data' => $tasksData,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => __('app.widgets.other_payments_egp'),
                    'data' => $paymentsData,
                    'borderColor' => 'rgb(245, 158, 11)',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $months,
        ];
    }
}
