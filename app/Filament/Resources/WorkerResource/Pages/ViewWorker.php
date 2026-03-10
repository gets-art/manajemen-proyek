<?php

namespace App\Filament\Resources\WorkerResource\Pages;

use App\Filament\Resources\WorkerResource\Widgets\WorkerPaymentsChart;
use App\Filament\Resources\WorkerResource\Widgets\WorkerStatsOverview;
use App\Filament\Resources\WorkerResource\WorkerResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewWorker extends ViewRecord
{
    protected static string $resource = WorkerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            WorkerStatsOverview::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            WorkerPaymentsChart::class,
        ];
    }
}
