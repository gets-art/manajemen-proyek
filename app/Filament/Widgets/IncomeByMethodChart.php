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
        $methods = PaymentMethod::query()
            ->select('name')
            ->withSum('payments', 'paid')
            ->having('payments_sum_paid', '>', 0)
            ->orderByDesc('payments_sum_paid')
            ->get();

        $bgColors = [
            'rgba(34, 197, 94, 0.6)',
            'rgba(59, 130, 246, 0.6)',
            'rgba(168, 85, 247, 0.6)',
            'rgba(249, 115, 22, 0.6)',
            'rgba(236, 72, 153, 0.6)',
            'rgba(20, 184, 166, 0.6)',
            'rgba(245, 158, 11, 0.6)',
            'rgba(99, 102, 241, 0.6)',
            'rgba(239, 68, 68, 0.6)',
            'rgba(107, 114, 128, 0.6)',
        ];

        $borderColors = [
            'rgba(34, 197, 94, 1)',
            'rgba(59, 130, 246, 1)',
            'rgba(168, 85, 247, 1)',
            'rgba(249, 115, 22, 1)',
            'rgba(236, 72, 153, 1)',
            'rgba(20, 184, 166, 1)',
            'rgba(245, 158, 11, 1)',
            'rgba(99, 102, 241, 1)',
            'rgba(239, 68, 68, 1)',
            'rgba(107, 114, 128, 1)',
        ];

        $hoverColors = [
            'rgba(34, 197, 94, 0.9)',
            'rgba(59, 130, 246, 0.9)',
            'rgba(168, 85, 247, 0.9)',
            'rgba(249, 115, 22, 0.9)',
            'rgba(236, 72, 153, 0.9)',
            'rgba(20, 184, 166, 0.9)',
            'rgba(245, 158, 11, 0.9)',
            'rgba(99, 102, 241, 0.9)',
            'rgba(239, 68, 68, 0.9)',
            'rgba(107, 114, 128, 0.9)',
        ];

        $count = $methods->count();

        return [
            'datasets' => [
                [
                    'data' => $methods->pluck('payments_sum_paid')->map(fn ($v) => (float) $v)->toArray(),
                    'backgroundColor' => array_slice($bgColors, 0, $count),
                    'borderColor' => array_slice($borderColors, 0, $count),
                    'hoverBackgroundColor' => array_slice($hoverColors, 0, $count),
                    'borderWidth' => 2,
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
