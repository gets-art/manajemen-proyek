<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class MonthlyRevenueChart extends ChartWidget
{
    protected static bool $isLazy = true;

    public function getHeading(): ?string
    {
        return __('app.widgets.monthly_revenue_expenses');
    }

    protected static ?int $sort = 6;

    protected int | string | array $columnSpan = 'full';

    protected ?string $maxHeight = '300px';

    protected ?string $pollingInterval = null;

    protected function getData(): array
    {
        $year = now()->year;

        $driver = DB::getDriverName();
        $monthQuery = $driver === 'sqlite' ? "CAST(strftime('%m', created_at) AS INTEGER)" : 'MONTH(created_at)';

        $incomeByMonth = DB::table('payments')
            ->selectRaw("$monthQuery as month, COALESCE(SUM(paid), 0) as total")
            ->where('paymentable_type', \App\Models\Project::class)
            ->whereNull('deleted_at')
            ->whereYear('created_at', $year)
            ->groupByRaw($monthQuery)
            ->pluck('total', 'month');

        $expensePaymentsByMonth = DB::table('payments')
            ->selectRaw("$monthQuery as month, COALESCE(SUM(paid), 0) as total")
            ->where('paymentable_type', '!=', \App\Models\Project::class)
            ->whereNull('deleted_at')
            ->whereYear('created_at', $year)
            ->groupByRaw($monthQuery)
            ->pluck('total', 'month');

        $systemExpensesByMonth = DB::table('expenses')
            ->selectRaw("$monthQuery as month, COALESCE(SUM(value), 0) as total")
            ->whereNull('deleted_at')
            ->whereYear('created_at', $year)
            ->groupByRaw($monthQuery)
            ->pluck('total', 'month');

        $incomeData = [];
        $expensesData = [];
        $profitData = [];
        $labels = [];

        for ($i = 1; $i <= 12; $i++) {
            $labels[] = date('M', mktime(0, 0, 0, $i, 1));
            $income = (float) ($incomeByMonth[$i] ?? 0);
            $expenses = (float) ($expensePaymentsByMonth[$i] ?? 0) + (float) ($systemExpensesByMonth[$i] ?? 0);
            $incomeData[] = $income;
            $expensesData[] = $expenses;
            $profitData[] = $income - $expenses;
        }

        return [
            'datasets' => [
                [
                    'label' => __('app.widgets.total_income'),
                    'data' => $incomeData,
                    'borderColor' => 'rgba(34, 197, 94, 1)',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => __('app.widgets.total_expenses'),
                    'data' => $expensesData,
                    'borderColor' => 'rgba(239, 68, 68, 1)',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => __('app.widgets.net_profit'),
                    'data' => $profitData,
                    'borderColor' => 'rgba(59, 130, 246, 1)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
