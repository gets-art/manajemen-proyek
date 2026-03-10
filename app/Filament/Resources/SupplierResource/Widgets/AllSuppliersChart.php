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
        $purchases = DB::table('purchase_tasks')
            ->selectRaw('MONTH(created_at) as month, SUM(total) as total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month')
            ->toArray();

        $payments = DB::table('payments')
            ->where('paymentable_type', 'App\\Models\\Supplier')
            ->selectRaw('MONTH(created_at) as month, SUM(paid) as total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
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
                    'label' => __('app.widgets.purchases_egp'),
                    'data' => $purchasesData,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => __('app.widgets.payments_egp'),
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
