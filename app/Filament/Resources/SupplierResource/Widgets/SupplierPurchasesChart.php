<?php

namespace App\Filament\Resources\SupplierResource\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class SupplierPurchasesChart extends ChartWidget
{
    public $record;

    public function getHeading(): ?string
    {
        return __('app.widgets.purchases_payments_over_time');
    }

    protected ?string $maxHeight = '300px';

    protected ?string $pollingInterval = null;

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $supplier = $this->record;

        $driver = \Illuminate\Support\Facades\DB::getDriverName();
        $monthQuery = $driver === 'sqlite' ? "CAST(strftime('%m', created_at) AS INTEGER)" : 'MONTH(created_at)';

        $purchases = $supplier->purchaseTasks()
            ->selectRaw("$monthQuery as month, SUM(total) as total")
            ->whereYear('created_at', now()->year)
            ->groupByRaw($monthQuery)
            ->pluck('total', 'month')
            ->toArray();

        $payments = $supplier->payments()
            ->selectRaw("$monthQuery as month, SUM(paid) as total")
            ->whereYear('created_at', now()->year)
            ->groupByRaw($monthQuery)
            ->pluck('total', 'month')
            ->toArray();

        $months = [];
        $purchasesData = [];
        $paymentsData = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[] = Carbon::create()->month($i)->format('M');
            $purchasesData[] = $purchases[$i] ?? 0;
            $paymentsData[] = $payments[$i] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => __('app.widgets.purchases_IDR'),
                    'data' => $purchasesData,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => __('app.widgets.payments_IDR'),
                    'data' => $paymentsData,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.5)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $months,
        ];
    }
}
