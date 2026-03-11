<?php

namespace App\Filament\Widgets;

use App\Models\ExpenseCategory;
use Filament\Widgets\ChartWidget;

class ExpensesByCategoryChart extends ChartWidget
{
    protected static bool $isLazy = true;

    public function getHeading(): ?string
    {
        return __('app.widgets.expenses_by_category');
    }

    protected static ?int $sort = 5;

    protected ?string $maxHeight = '300px';

    protected ?string $pollingInterval = null;

    protected function getData(): array
    {
        $categories = ExpenseCategory::query()
            ->select('name')
            ->withSum('expenses', 'value')
            ->having('expenses_sum_value', '>', 0)
            ->orderByDesc('expenses_sum_value')
            ->limit(10)
            ->get();

        $colors = [
            'rgba(239, 68, 68, 0.7)',
            'rgba(249, 115, 22, 0.7)',
            'rgba(245, 158, 11, 0.7)',
            'rgba(168, 85, 247, 0.7)',
            'rgba(236, 72, 153, 0.7)',
            'rgba(59, 130, 246, 0.7)',
            'rgba(34, 197, 94, 0.7)',
            'rgba(20, 184, 166, 0.7)',
            'rgba(99, 102, 241, 0.7)',
            'rgba(107, 114, 128, 0.7)',
        ];

        return [
            'datasets' => [
                [
                    'data' => $categories->pluck('expenses_sum_value')->map(fn ($v) => (float) $v)->toArray(),
                    'backgroundColor' => array_slice($colors, 0, $categories->count()),
                ],
            ],
            'labels' => $categories->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
