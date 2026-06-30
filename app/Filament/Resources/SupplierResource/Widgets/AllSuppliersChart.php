<?php

namespace App\Filament\Resources\SupplierResource\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AllSuppliersChart extends ChartWidget
{
    public function getHeading(): ?string
    {
        return __('app.widgets.purchases_payments_over_time');
    }

    protected ?string $maxHeight = '300px';

    protected ?string $pollingInterval = null;

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $year = now()->year;
        
        $driver = DB::getDriverName();
        $monthQuery = $driver === 'sqlite' ? "CAST(strftime('%m', created_at) AS INTEGER)" : 'MONTH(created_at)';

        $purchases = \App\Models\PurchaseTask::query()
            ->selectRaw("$monthQuery as month, SUM(total) as total")
            ->whereYear('created_at', $year)
            ->groupByRaw($monthQuery)
            ->pluck('total', 'month')
            ->toArray();

        $payments = \App\Models\Payment::query()
            ->where('paymentable_type', \App\Models\Supplier::class)
            ->selectRaw("$monthQuery as month, SUM(paid) as total")
            ->whereYear('created_at', $year)
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
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => __('app.widgets.payments_IDR'),
                    'data' => $paymentsData,
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
