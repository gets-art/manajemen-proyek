<?php

namespace App\Filament\Pages;

use App\Models\Supplier;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;

class SuppliersReport extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.suppliers-report';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-truck';

    protected static string | \UnitEnum | null $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string { return __('app.nav_groups.reports'); }
    public static function getNavigationLabel(): string { return __('app.reports.suppliers.nav_label'); }

    public function getTitle(): string { return __('app.reports.suppliers.title'); }

    public ?string $date_from = null;
    public ?string $date_to = null;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('date_from')
                    ->label(__('app.reports.from'))
                    ->reactive(),
                DatePicker::make('date_to')
                    ->label(__('app.reports.to'))
                    ->reactive(),
            ])
            ->columns(2)
            ->statePath(null);
    }

    public function getReportData(): array
    {
        $suppliers = Supplier::where('active', true)->get()->map(function ($supplier) {
            $purchasesQuery = $supplier->purchaseTasks();
            $paymentsQuery = $supplier->payments();

            if ($this->date_from) {
                $purchasesQuery->whereDate('purchase_tasks.created_at', '>=', $this->date_from);
                $paymentsQuery->whereDate('payments.created_at', '>=', $this->date_from);
            }
            if ($this->date_to) {
                $purchasesQuery->whereDate('purchase_tasks.created_at', '<=', $this->date_to);
                $paymentsQuery->whereDate('payments.created_at', '<=', $this->date_to);
            }

            $purchasesTotal = $purchasesQuery->sum('final_total');
            $paymentsTotal = $paymentsQuery->sum('paid');

            return [
                'name' => $supplier->name,
                'phone' => $supplier->phone,
                'purchases_total' => $purchasesTotal,
                'payments_total' => $paymentsTotal,
                'balance' => $purchasesTotal - $paymentsTotal,
            ];
        })->sortByDesc('purchases_total')->values();

        return [
            'suppliers' => $suppliers,
            'totalPurchases' => $suppliers->sum('purchases_total'),
            'totalPayments' => $suppliers->sum('payments_total'),
            'totalBalance' => $suppliers->sum('balance'),
            'suppliersCount' => $suppliers->count(),
        ];
    }
}
