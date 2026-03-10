<?php

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource\ClientResource;
use App\Filament\Resources\ClientResource\Widgets\AllClientsChart;
use App\Filament\Resources\ClientResource\Widgets\AllClientsStatsOverview;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListClients extends ListRecords
{
    protected static string $resource = ClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            AllClientsStatsOverview::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            AllClientsChart::class,
        ];
    }
}
