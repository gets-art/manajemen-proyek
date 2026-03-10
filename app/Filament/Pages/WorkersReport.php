<?php

namespace App\Filament\Pages;

use App\Models\Worker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;

class WorkersReport extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.workers-report';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';

    protected static string | \UnitEnum | null $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string { return __('app.nav_groups.reports'); }
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
        $workers = Worker::where('active', true)->get()->map(function ($worker) {
            $tasksQuery = $worker->tasks();
            $paymentsQuery = $worker->payments();

            if ($this->date_from) {
                $tasksQuery->whereDate('tasks.created_at', '>=', $this->date_from);
                $paymentsQuery->whereDate('payments.created_at', '>=', $this->date_from);
            }
            if ($this->date_to) {
                $tasksQuery->whereDate('tasks.created_at', '<=', $this->date_to);
                $paymentsQuery->whereDate('payments.created_at', '<=', $this->date_to);
            }

            $tasksCount = $tasksQuery->count();
            $tasksPaid = $tasksQuery->sum('task_workers.paid');
            $paymentsTotal = $paymentsQuery->sum('paid');

            return [
                'name' => $worker->name,
                'phone' => $worker->phone_number,
                'tasks_count' => $tasksCount,
                'tasks_paid' => $tasksPaid,
                'payments_total' => $paymentsTotal,
                'grand_total' => $tasksPaid + $paymentsTotal,
            ];
        })->sortByDesc('grand_total')->values();

        return [
            'workers' => $workers,
            'totalTasksPaid' => $workers->sum('tasks_paid'),
            'totalPayments' => $workers->sum('payments_total'),
            'totalGrand' => $workers->sum('grand_total'),
            'workersCount' => $workers->count(),
        ];
    }
}
