<?php

namespace App\Filament\Pages;

use App\Models\Client;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;

class ClientsReport extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.clients-report';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-group';

    protected static string | \UnitEnum | null $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string { return __('app.nav_groups.reports'); }
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
        $clients = Client::all()->map(function ($client) {
            $projectsQuery = $client->projects();

            if ($this->date_from) {
                $projectsQuery->whereDate('projects.created_at', '>=', $this->date_from);
            }
            if ($this->date_to) {
                $projectsQuery->whereDate('projects.created_at', '<=', $this->date_to);
            }

            $projectsCount = $projectsQuery->count();
            $totalFinal = $projectsQuery->sum('final_total');
            $totalPaid = $projectsQuery->sum('paid_total');
            $totalRest = $projectsQuery->sum('rest_total');

            return [
                'name' => $client->name,
                'phone' => $client->phone,
                'email' => $client->email,
                'projects_count' => $projectsCount,
                'total_final' => $totalFinal,
                'total_paid' => $totalPaid,
                'total_rest' => $totalRest,
            ];
        })->sortByDesc('total_final')->values();

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
