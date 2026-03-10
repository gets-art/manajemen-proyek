<?php

namespace App\Filament\Resources\SupplierResource\Pages;

use App\Filament\Resources\SupplierResource\SupplierResource;
use App\Filament\Resources\SupplierResource\Widgets\SupplierPurchasesChart;
use App\Filament\Resources\SupplierResource\Widgets\SupplierStatsOverview;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSupplier extends ViewRecord
{
    protected static string $resource = SupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            SupplierStatsOverview::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            SupplierPurchasesChart::class,
        ];
    }
}
