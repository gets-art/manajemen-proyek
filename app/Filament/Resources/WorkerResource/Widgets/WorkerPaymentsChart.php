<?php

namespace App\Filament\Resources\WorkerResource\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class WorkerPaymentsChart extends ChartWidget
{
    public $record;

    public function getHeading(): ?string
    {
        return __('app.widgets.payments_over_time');
    }

    protected ?string $maxHeight = '300px';

    protected ?string $pollingInterval = null;

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $worker = $this->record;

        $taskPayments = DB::table('task_workers')
            ->where('worker_id', $worker->id)
            ->whereYear('created_at', now()->year)
            ->selectRaw('MONTH(created_at) as month, SUM(paid) as total')
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month')
            ->toArray();

        $otherPayments = $worker->payments()
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
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => __('app.widgets.other_payments_egp'),
                    'data' => $paymentsData,
                    'backgroundColor' => 'rgba(245, 158, 11, 0.5)',
                    'borderColor' => 'rgb(245, 158, 11)',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $months,
        ];
    }
}
