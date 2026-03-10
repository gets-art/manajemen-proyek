<?php

namespace App\Filament\Resources\ProjectResource\Widgets;

use Filament\Widgets\ChartWidget;

class ProjectExpensesChart extends ChartWidget
{
    public $record;

    public function getHeading(): ?string
    {
        return __('app.widgets.expenses_by_category');
    }

    protected ?string $maxHeight = '300px';

    protected ?string $pollingInterval = null;

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $project = $this->record;

        $expenses = $project->expenses()
            ->join('expense_categories', 'expenses.expense_category_id', '=', 'expense_categories.id')
            ->selectRaw('expense_categories.name as category, SUM(expenses.value) as total')
            ->groupBy('expense_categories.name')
            ->pluck('total', 'category')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => __('app.widgets.expenses_egp'),
                    'data' => array_values($expenses),
                    'backgroundColor' => 'rgba(239, 68, 68, 0.5)',
                    'borderColor' => 'rgb(239, 68, 68)',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => array_keys($expenses),
        ];
    }
}
