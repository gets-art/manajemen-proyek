<?php

namespace App\Filament\Pages;

use App\Models\Supplier;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use App\Filament\NavigationGroup;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;

class SuppliersReport extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.suppliers-report';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-truck';

    protected static string | \UnitEnum | null $navigationGroup = NavigationGroup::Reports;

    protected static ?int $navigationSort = 2;
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
        $purchasesSubquery = DB::table('purchase_tasks')
            ->select('supplier_id', DB::raw('COALESCE(SUM(final_total), 0) as purchases_total'));

        if ($this->date_from) {
            $purchasesSubquery->whereDate('created_at', '>=', $this->date_from);
        }
        if ($this->date_to) {
            $purchasesSubquery->whereDate('created_at', '<=', $this->date_to);
        }

        $purchasesSubquery = $purchasesSubquery->groupBy('supplier_id');

        $paymentsSubquery = DB::table('payments')
            ->select('paymentable_id', DB::raw('COALESCE(SUM(paid), 0) as payments_total'))
            ->where('paymentable_type', \App\Models\Supplier::class)
            ->whereNull('deleted_at');

        if ($this->date_from) {
            $paymentsSubquery->whereDate('created_at', '>=', $this->date_from);
        }
        if ($this->date_to) {
            $paymentsSubquery->whereDate('created_at', '<=', $this->date_to);
        }

        $paymentsSubquery = $paymentsSubquery->groupBy('paymentable_id');

        $suppliers = Supplier::query()
            ->select([
                'suppliers.id',
                'suppliers.name',
                'suppliers.phone',
                DB::raw('COALESCE(pt.purchases_total, 0) as purchases_total'),
                DB::raw('COALESCE(pm.payments_total, 0) as payments_total'),
                DB::raw('COALESCE(pt.purchases_total, 0) - COALESCE(pm.payments_total, 0) as balance'),
            ])
            ->leftJoinSub($purchasesSubquery, 'pt', 'pt.supplier_id', '=', 'suppliers.id')
            ->leftJoinSub($paymentsSubquery, 'pm', 'pm.paymentable_id', '=', 'suppliers.id')
            ->where('suppliers.active', true)
            ->whereNull('suppliers.deleted_at')
            ->orderByDesc('purchases_total')
            ->get();

        return [
            'suppliers' => $suppliers,
            'totalPurchases' => $suppliers->sum('purchases_total'),
            'totalPayments' => $suppliers->sum('payments_total'),
            'totalBalance' => $suppliers->sum('balance'),
            'suppliersCount' => $suppliers->count(),
        ];
    }
}
