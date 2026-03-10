<?php

namespace App\Filament\Resources\ProjectResource\Widgets;

use App\Models\Project;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class AllProjectsChart extends ChartWidget
{
    public function getHeading(): ?string
    {
        return __('app.widgets.projects_overview');
    }

    protected ?string $maxHeight = '300px';

    protected ?string $pollingInterval = null;

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $payments = Project::selectRaw('MONTH(created_at) as month, SUM(final_total) as value, SUM(paid_total) as paid')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->get()
            ->keyBy('month');

        $months = [];
        $valueData = [];
        $paidData = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[] = Carbon::create()->month($i)->format('M');
            $valueData[] = $payments->get($i)?->value ?? 0;
            $paidData[] = $payments->get($i)?->paid ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => __('app.widgets.total_value_egp'),
                    'data' => $valueData,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => __('app.widgets.total_paid_egp'),
                    'data' => $paidData,
                    'borderColor' => 'rgb(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $months,
        ];
    }
}
