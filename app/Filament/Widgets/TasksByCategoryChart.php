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
        $categories = Category::query()
            ->select('name')
            ->withCount('tasks')
            ->get()
            ->filter(fn ($cat) => $cat->tasks_count > 0)
            ->sortByDesc('tasks_count')
            ->take(10);

        $bgColors = [
            'rgba(59, 130, 246, 0.6)',
            'rgba(34, 197, 94, 0.6)',
            'rgba(249, 115, 22, 0.6)',
            'rgba(239, 68, 68, 0.6)',
            'rgba(168, 85, 247, 0.6)',
            'rgba(236, 72, 153, 0.6)',
            'rgba(20, 184, 166, 0.6)',
            'rgba(245, 158, 11, 0.6)',
            'rgba(99, 102, 241, 0.6)',
            'rgba(107, 114, 128, 0.6)',
        ];

        $borderColors = [
            'rgba(59, 130, 246, 1)',
            'rgba(34, 197, 94, 1)',
            'rgba(249, 115, 22, 1)',
            'rgba(239, 68, 68, 1)',
            'rgba(168, 85, 247, 1)',
            'rgba(236, 72, 153, 1)',
            'rgba(20, 184, 166, 1)',
            'rgba(245, 158, 11, 1)',
            'rgba(99, 102, 241, 1)',
            'rgba(107, 114, 128, 1)',
        ];

        $hoverColors = [
            'rgba(59, 130, 246, 0.9)',
            'rgba(34, 197, 94, 0.9)',
            'rgba(249, 115, 22, 0.9)',
            'rgba(239, 68, 68, 0.9)',
            'rgba(168, 85, 247, 0.9)',
            'rgba(236, 72, 153, 0.9)',
            'rgba(20, 184, 166, 0.9)',
            'rgba(245, 158, 11, 0.9)',
            'rgba(99, 102, 241, 0.9)',
            'rgba(107, 114, 128, 0.9)',
        ];

        $count = $categories->count();

        return [
            'datasets' => [
                [
                    'label' => __('app.widgets.tasks'),
                    'data' => $categories->pluck('tasks_count')->toArray(),
                    'backgroundColor' => array_slice($bgColors, 0, $count),
                    'borderColor' => array_slice($borderColors, 0, $count),
                    'hoverBackgroundColor' => array_slice($hoverColors, 0, $count),
                    'borderWidth' => 2,
                    'borderRadius' => 6,
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
