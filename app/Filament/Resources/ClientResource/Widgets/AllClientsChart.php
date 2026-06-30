<?php

namespace App\Filament\Resources\ClientResource\Widgets;

use App\Models\Client;
use Filament\Widgets\ChartWidget;

class AllClientsChart extends ChartWidget
{
    public function getHeading(): ?string
    {
        return __('app.widgets.top_clients_by_value');
    }

    protected ?string $maxHeight = '300px';

    protected ?string $pollingInterval = null;

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $clients = Client::withSum('projects', 'final_total')
            ->withSum('projects', 'paid_total')
            ->orderByDesc('projects_sum_final_total')
            ->limit(10)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => __('app.widgets.total_value_IDR'),
                    'data' => $clients->pluck('projects_sum_final_total')->map(fn ($v) => $v ?? 0)->toArray(),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.7)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 1,
                ],
                [
                    'label' => __('app.widgets.paid_IDR'),
                    'data' => $clients->pluck('projects_sum_paid_total')->map(fn ($v) => $v ?? 0)->toArray(),
                    'backgroundColor' => 'rgba(16, 185, 129, 0.7)',
                    'borderColor' => 'rgb(16, 185, 129)',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $clients->pluck('name')->toArray(),
        ];
    }
}
