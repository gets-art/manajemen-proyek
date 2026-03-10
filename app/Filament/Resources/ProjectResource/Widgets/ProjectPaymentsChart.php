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
        return 'doughnut';
    }

    protected function getData(): array
    {
        $project = $this->record;

        $payments = $project->payments()
            ->selectRaw('MONTH(created_at) as month, SUM(paid) as total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month')
            ->toArray();

        $colors = [
            'rgba(59, 130, 246, 0.7)',
            'rgba(16, 185, 129, 0.7)',
            'rgba(245, 158, 11, 0.7)',
            'rgba(239, 68, 68, 0.7)',
            'rgba(139, 92, 246, 0.7)',
            'rgba(236, 72, 153, 0.7)',
            'rgba(20, 184, 166, 0.7)',
            'rgba(249, 115, 22, 0.7)',
            'rgba(99, 102, 241, 0.7)',
            'rgba(234, 179, 8, 0.7)',
            'rgba(168, 85, 247, 0.7)',
            'rgba(14, 165, 233, 0.7)',
        ];

        $labels = [];
        $data = [];
        foreach ($payments as $month => $total) {
            $labels[] = Carbon::create()->month($month)->format('M');
            $data[] = $total;
        }

        return [
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => array_slice($colors, 0, count($data)),
                ],
            ],
            'labels' => $labels,
        ];
    }
}
