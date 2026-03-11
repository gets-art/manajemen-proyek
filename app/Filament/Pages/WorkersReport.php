<?php

namespace App\Filament\Pages;

use App\Models\Worker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use App\Filament\NavigationGroup;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;

class WorkersReport extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.workers-report';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';

    protected static string | \UnitEnum | null $navigationGroup = NavigationGroup::Reports;

    protected static ?int $navigationSort = 3;
    public static function getNavigationLabel(): string { return __('app.reports.workers.nav_label'); }

    public function getTitle(): string { return __('app.reports.workers.title'); }

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
        $tasksPaidSubquery = DB::table('task_workers')
            ->select('task_workers.worker_id', DB::raw('COUNT(task_workers.task_id) as tasks_count'), DB::raw('COALESCE(SUM(task_workers.paid), 0) as tasks_paid'))
            ->join('tasks', 'tasks.id', '=', 'task_workers.task_id')
            ->whereNull('tasks.deleted_at');

        if ($this->date_from) {
            $tasksPaidSubquery->whereDate('tasks.created_at', '>=', $this->date_from);
        }
        if ($this->date_to) {
            $tasksPaidSubquery->whereDate('tasks.created_at', '<=', $this->date_to);
        }

        $tasksPaidSubquery = $tasksPaidSubquery->groupBy('task_workers.worker_id');

        $paymentsSubquery = DB::table('payments')
            ->select('payments.paymentable_id', DB::raw('COALESCE(SUM(payments.paid), 0) as payments_total'))
            ->where('payments.paymentable_type', \App\Models\Worker::class)
            ->whereNull('payments.deleted_at');

        if ($this->date_from) {
            $paymentsSubquery->whereDate('payments.created_at', '>=', $this->date_from);
        }
        if ($this->date_to) {
            $paymentsSubquery->whereDate('payments.created_at', '<=', $this->date_to);
        }

        $paymentsSubquery = $paymentsSubquery->groupBy('payments.paymentable_id');

        $workers = Worker::query()
            ->select([
                'workers.id',
                'workers.name',
                'workers.phone_number',
                DB::raw('COALESCE(tw.tasks_count, 0) as tasks_count'),
                DB::raw('COALESCE(tw.tasks_paid, 0) as tasks_paid'),
                DB::raw('COALESCE(pm.payments_total, 0) as payments_total'),
                DB::raw('COALESCE(tw.tasks_paid, 0) + COALESCE(pm.payments_total, 0) as grand_total'),
            ])
            ->leftJoinSub($tasksPaidSubquery, 'tw', 'tw.worker_id', '=', 'workers.id')
            ->leftJoinSub($paymentsSubquery, 'pm', 'pm.paymentable_id', '=', 'workers.id')
            ->where('workers.active', true)
            ->whereNull('workers.deleted_at')
            ->orderByDesc('grand_total')
            ->get();

        return [
            'workers' => $workers,
            'totalTasksPaid' => $workers->sum('tasks_paid'),
            'totalPayments' => $workers->sum('payments_total'),
            'totalGrand' => $workers->sum('grand_total'),
            'workersCount' => $workers->count(),
        ];
    }
}
