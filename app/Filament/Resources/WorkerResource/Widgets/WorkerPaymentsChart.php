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
        $isSqlite = DB::getDriverName() === 'sqlite';
        $periodSelector = $isSqlite ? "strftime('%Y-%m', created_at)" : "DATE_FORMAT(created_at, '%Y-%m')";

        $taskPayments = DB::table('task_workers')
            ->where('worker_id', $worker->id)
            ->selectRaw("{$periodSelector} as period, SUM(paid) as total")
            ->groupByRaw($periodSelector)
            ->pluck('total', 'period')
            ->toArray();

        $otherPayments = $worker->payments()
            ->selectRaw("{$periodSelector} as period, SUM(paid) as total")
            ->groupByRaw($periodSelector)
            ->pluck('total', 'period')
            ->toArray();

        $allPeriods = collect(array_keys($taskPayments))
            ->merge(array_keys($otherPayments))
            ->unique()
            ->sort()
            ->values();

        $labels = [];
        $tasksData = [];
        $paymentsData = [];
        foreach ($allPeriods as $period) {
            $labels[] = Carbon::parse($period . '-01')->format('M Y');
            $tasksData[] = $taskPayments[$period] ?? 0;
            $paymentsData[] = $otherPayments[$period] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => __('app.widgets.tasks_paid_IDR'),
                    'data' => $tasksData,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => __('app.widgets.other_payments_IDR'),
                    'data' => $paymentsData,
                    'backgroundColor' => 'rgba(245, 158, 11, 0.5)',
                    'borderColor' => 'rgb(245, 158, 11)',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
