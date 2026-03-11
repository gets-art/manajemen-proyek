<?php

namespace App\Filament\Pages;

use App\Models\Client;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use App\Filament\NavigationGroup;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;

class ClientsReport extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.clients-report';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-group';

    protected static string | \UnitEnum | null $navigationGroup = NavigationGroup::Reports;

    protected static ?int $navigationSort = 4;
    public static function getNavigationLabel(): string { return __('app.reports.clients.nav_label'); }

    public function getTitle(): string { return __('app.reports.clients.title'); }

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
        $query = Client::query()
            ->select([
                'clients.id',
                'clients.name',
                'clients.phone',
                'clients.email',
                DB::raw('COUNT(projects.id) as projects_count'),
                DB::raw('COALESCE(SUM(projects.final_total), 0) as total_final'),
                DB::raw('COALESCE(SUM(projects.paid_total), 0) as total_paid'),
                DB::raw('COALESCE(SUM(projects.rest_total), 0) as total_rest'),
            ])
            ->leftJoin('projects', function ($join) {
                $join->on('clients.id', '=', 'projects.client_id')
                    ->whereNull('projects.deleted_at');

                if ($this->date_from) {
                    $join->whereDate('projects.created_at', '>=', $this->date_from);
                }
                if ($this->date_to) {
                    $join->whereDate('projects.created_at', '<=', $this->date_to);
                }
            })
            ->whereNull('clients.deleted_at')
            ->groupBy('clients.id', 'clients.name', 'clients.phone', 'clients.email')
            ->orderByDesc('total_final');

        $clients = $query->get();

        return [
            'clients' => $clients,
            'grandFinal' => $clients->sum('total_final'),
            'grandPaid' => $clients->sum('total_paid'),
            'grandRest' => $clients->sum('total_rest'),
            'clientsCount' => $clients->count(),
            'totalProjects' => $clients->sum('projects_count'),
        ];
    }
}
