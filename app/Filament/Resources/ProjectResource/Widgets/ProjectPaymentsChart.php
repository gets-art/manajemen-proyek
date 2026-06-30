<?php

namespace App\Filament\Resources\ProjectResource\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class ProjectPaymentsChart extends ChartWidget
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
        return 'line';
    }

    protected function getData(): array
    {
        $project = $this->record;
        $isSqlite = \Illuminate\Support\Facades\DB::getDriverName() === 'sqlite';
        $periodSelector = $isSqlite ? "strftime('%Y-%m', created_at)" : "DATE_FORMAT(created_at, '%Y-%m')";

        $payments = $project->payments()
            ->selectRaw("{$periodSelector} as period, SUM(paid) as total")
            ->groupByRaw($periodSelector)
            ->orderByRaw($periodSelector)
            ->pluck('total', 'period')
            ->toArray();

        $labels = [];
        $data = [];
        foreach ($payments as $period => $total) {
            $labels[] = Carbon::parse($period . '-01')->format('M Y');
            $data[] = $total;
        }

        return [
            'datasets' => [
                [
                    'label' => __('app.fields.amount') . ' (IDR)',
                    'data' => $data,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
