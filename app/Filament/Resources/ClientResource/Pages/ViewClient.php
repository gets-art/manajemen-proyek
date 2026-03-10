<?php

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource\ClientResource;
use App\Filament\Resources\ClientResource\Widgets\ClientProjectsChart;
use App\Filament\Resources\ClientResource\Widgets\ClientStatsOverview;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewClient extends ViewRecord
{
    protected static string $resource = ClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ClientStatsOverview::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            ClientProjectsChart::class,
        ];
    }
}
