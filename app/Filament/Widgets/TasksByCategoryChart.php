<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use Filament\Widgets\ChartWidget;

class TasksByCategoryChart extends ChartWidget
{
    protected static bool $isLazy = true;

    public function getHeading(): ?string
    {
        return __('app.widgets.tasks_by_category');
    }

    protected static ?int $sort = 2;

    protected ?string $maxHeight = '300px';

    protected ?string $pollingInterval = null;

    protected function getData(): array
    {
        $categories = Category::withCount('tasks')
            ->having('tasks_count', '>', 0)
            ->orderByDesc('tasks_count')
            ->limit(10)
            ->get();

        $colors = [
            'rgba(59, 130, 246, 0.7)',
            'rgba(34, 197, 94, 0.7)',
            'rgba(249, 115, 22, 0.7)',
            'rgba(239, 68, 68, 0.7)',
            'rgba(168, 85, 247, 0.7)',
            'rgba(236, 72, 153, 0.7)',
            'rgba(20, 184, 166, 0.7)',
            'rgba(245, 158, 11, 0.7)',
            'rgba(99, 102, 241, 0.7)',
            'rgba(107, 114, 128, 0.7)',
        ];

        return [
            'datasets' => [
                [
                    'label' => __('app.widgets.tasks'),
                    'data' => $categories->pluck('tasks_count')->toArray(),
                    'backgroundColor' => array_slice($colors, 0, $categories->count()),
                    'borderColor' => array_slice($colors, 0, $categories->count()),
                ],
            ],
            'labels' => $categories->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
