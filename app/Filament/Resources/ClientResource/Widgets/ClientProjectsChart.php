<?php

namespace App\Filament\Resources\ClientResource\Widgets;

use Filament\Widgets\ChartWidget;

class ClientProjectsChart extends ChartWidget
{
    public $record;

    public function getHeading(): ?string
    {
        return __('app.widgets.projects_by_status');
    }

    protected ?string $maxHeight = '300px';

    protected ?string $pollingInterval = null;

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $client = $this->record;

        $statusLabels = [
            0 => __('app.statuses.pending'),
            1 => __('app.statuses.in_progress'),
            2 => __('app.statuses.completed'),
            3 => __('app.statuses.cancelled'),
        ];

        $statusColors = [
            0 => 'rgba(245, 158, 11, 0.7)',
            1 => 'rgba(59, 130, 246, 0.7)',
            2 => 'rgba(16, 185, 129, 0.7)',
            3 => 'rgba(239, 68, 68, 0.7)',
        ];

        $projects = $client->projects()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $labels = [];
        $data = [];
        $colors = [];

        foreach ($projects as $status => $count) {
            $labels[] = $statusLabels[$status] ?? __('app.statuses.unknown');
            $data[] = $count;
            $colors[] = $statusColors[$status] ?? 'rgba(107, 114, 128, 0.7)';
        }

        return [
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => $colors,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
