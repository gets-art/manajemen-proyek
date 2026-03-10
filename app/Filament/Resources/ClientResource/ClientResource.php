<?php

namespace App\Filament\Resources\ClientResource;

use App\Filament\Resources\ClientResource\Pages\CreateClient;
use App\Filament\Resources\ClientResource\Pages\EditClient;
use App\Filament\Resources\ClientResource\Pages\ListClients;
use App\Filament\Resources\ClientResource\Pages\ViewClient;
use App\Filament\Resources\ClientResource\RelationManagers\PaymentsRelationManager;
use App\Filament\Resources\ClientResource\RelationManagers\ProjectsRelationManager;
use App\Filament\Resources\ClientResource\Schemas\ClientForm;
use App\Filament\Resources\ClientResource\Schemas\ClientInfolist;
use App\Filament\Resources\ClientResource\Tables\ClientsTable;
use App\Filament\Resources\ClientResource\Widgets\AllClientsChart;
use App\Filament\Resources\ClientResource\Widgets\AllClientsStatsOverview;
use App\Filament\Resources\ClientResource\Widgets\ClientProjectsChart;
use App\Filament\Resources\ClientResource\Widgets\ClientStatsOverview;
use App\Models\Client;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-group';
    protected static string | \UnitEnum | null $navigationGroup = 'Project Management';

    public static function getNavigationGroup(): ?string { return __('app.nav_groups.project_management'); }
    public static function getModelLabel(): string { return __('app.resources.client.label'); }
    public static function getPluralModelLabel(): string { return __('app.resources.client.plural'); }
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return ClientForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ClientInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClientsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ProjectsRelationManager::class,
            PaymentsRelationManager::class,
        ];
    }

    public static function getWidgets(): array
    {
        return [
            ClientStatsOverview::class,
            ClientProjectsChart::class,
            AllClientsStatsOverview::class,
            AllClientsChart::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClients::route('/'),
            'create' => CreateClient::route('/create'),
            'view' => ViewClient::route('/{record}'),
            'edit' => EditClient::route('/{record}/edit'),
        ];
    }
}
