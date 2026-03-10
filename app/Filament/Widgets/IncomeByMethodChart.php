<?php

namespace App\Filament\Widgets;

use App\Models\PaymentMethod;
use Filament\Widgets\ChartWidget;

class IncomeByMethodChart extends ChartWidget
{
    protected static bool $isLazy = true;

    public function getHeading(): ?string
    {
        return __('app.widgets.income_by_method');
    }

    protected static ?int $sort = 3;

    protected ?string $maxHeight = '300px';

    protected ?string $pollingInterval = null;

    protected function getData(): array
    {
        $methods = PaymentMethod::withSum('payments', 'paid')
            ->having('payments_sum_paid', '>', 0)
            ->orderByDesc('payments_sum_paid')
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
                    'data' => $methods->pluck('payments_sum_paid')->map(fn ($v) => (float) $v)->toArray(),
                    'backgroundColor' => array_slice($colors, 0, $methods->count()),
                ],
            ],
            'labels' => $methods->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
