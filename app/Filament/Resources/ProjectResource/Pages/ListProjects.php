<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource\ProjectResource;
use App\Filament\Resources\ProjectResource\Widgets\AllProjectsChart;
use App\Filament\Resources\ProjectResource\Widgets\AllProjectsStatsOverview;
use App\Filament\Resources\ProjectResource\Widgets\AllProjectsStatusChart;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProjects extends ListRecords
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            AllProjectsStatsOverview::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            AllProjectsChart::class,
            AllProjectsStatusChart::class,
        ];
    }
}
