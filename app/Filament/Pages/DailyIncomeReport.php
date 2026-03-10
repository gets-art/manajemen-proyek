<?php

namespace App\Filament\Pages;

use App\Models\Expense;
use App\Models\Payment;
use App\Models\PaymentMethod;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;

class DailyIncomeReport extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.daily-income-report';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-banknotes';

    protected static string | \UnitEnum | null $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string { return __('app.nav_groups.reports'); }
    public static function getNavigationLabel(): string { return __('app.daily_income_report.nav_label'); }

    public ?string $activePreset = 'all';
    public ?string $date_from = null;
    public ?string $date_to = null;
    public bool $showIncome = false;
    public bool $showExpenses = false;

    public function getTitle(): string
    {
        return __('app.daily_income_report.title');
    }

    public function mount(): void
    {
        // default: all time
    }

    public function setPreset(string $preset): void
    {
        $this->activePreset = $preset;

        match ($preset) {
            'all'       => $this->resetDates(),
            'today'     => $this->setDates(today(), today()),
            'yesterday' => $this->setDates(today()->subDay(), today()->subDay()),
            'custom'    => null,
        };
    }

    protected function resetDates(): void
    {
        $this->date_from = null;
        $this->date_to = null;
    }

    protected function setDates(Carbon $from, Carbon $to): void
    {
        $this->date_from = $from->toDateString();
        $this->date_to = $to->toDateString();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('date_from')
                    ->label(__('app.daily_income_report.from'))
                    ->reactive()
                    ->afterStateUpdated(fn () => $this->activePreset = 'custom'),
                DatePicker::make('date_to')
                    ->label(__('app.daily_income_report.to'))
                    ->reactive()
                    ->afterStateUpdated(fn () => $this->activePreset = 'custom'),
            ])
            ->columns(2)
            ->statePath(null);
    }

    public function toggleIncome(): void
    {
        $this->showIncome = !$this->showIncome;
    }

    public function toggleExpenses(): void
    {
        $this->showExpenses = !$this->showExpenses;
    }

    protected function applyDateFilters($query, string $dateColumn = 'created_at')
    {
        if ($this->date_from) {
            $query->whereDate($dateColumn, '>=', $this->date_from);
        }
        if ($this->date_to) {
            $query->whereDate($dateColumn, '<=', $this->date_to);
        }
        return $query;
    }

    public function getReportData(): array
    {
        // Income = project payments
        $incomeQuery = $this->applyDateFilters(
            Payment::where('paymentable_type', \App\Models\Project::class)
        );
        $income = (clone $incomeQuery)->sum('paid');

        // Expenses = non-project payments + system expenses
        $expensePaymentsQuery = $this->applyDateFilters(
            Payment::where('paymentable_type', '!=', \App\Models\Project::class)
        );
        $systemExpensesQuery = $this->applyDateFilters(Expense::query());

        $expenses = (clone $expensePaymentsQuery)->sum('paid') + (clone $systemExpensesQuery)->sum('value');

        // Payment methods breakdown (project payments + system expenses merged)
        $projectByMethod = $this->applyDateFilters(
            Payment::where('paymentable_type', \App\Models\Project::class)->whereHas('paymentMethod')
        )->selectRaw('payment_method_id, sum(paid) as total')->groupBy('payment_method_id')->get();

        $expenseByMethod = $this->applyDateFilters(
            Expense::whereNotNull('payment_method_id')
        )->selectRaw('payment_method_id, sum(value) as total')->groupBy('payment_method_id')->get();

        $allMethodIds = $projectByMethod->pluck('payment_method_id')
            ->merge($expenseByMethod->pluck('payment_method_id'))
            ->unique();

        $methods = PaymentMethod::whereIn('id', $allMethodIds)->get();

        $paymentMethods = $methods->map(function ($method) use ($projectByMethod, $expenseByMethod) {
            $projectTotal = $projectByMethod->where('payment_method_id', $method->id)->first()->total ?? 0;
            $expenseTotal = $expenseByMethod->where('payment_method_id', $method->id)->first()->total ?? 0;
            return [
                'name' => $method->name,
                'image' => $method->image,
                'total' => $projectTotal + $expenseTotal,
            ];
        })->filter(fn ($item) => $item['total'] > 0)->values();

        return [
            'income' => $income,
            'expenses' => $expenses,
            'paymentMethods' => $paymentMethods,
        ];
    }

    public function getIncomeDetails(): \Illuminate\Support\Collection
    {
        return $this->applyDateFilters(
            Payment::where('paymentable_type', \App\Models\Project::class)
        )->with(['paymentMethod', 'paymentable'])->latest()->get();
    }

    public function getExpenseDetails(): array
    {
        $paid = $this->applyDateFilters(
            Payment::where('paymentable_type', '!=', \App\Models\Project::class)
        )->with(['paymentMethod', 'paymentable'])->latest()->get();

        $systemExpenses = $this->applyDateFilters(Expense::query())
            ->with(['paymentMethod', 'addedBy'])->latest()->get();

        return [
            'paid' => $paid,
            'systemExpenses' => $systemExpenses,
        ];
    }
}
