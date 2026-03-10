<?php

namespace App\Filament\Resources\SupplierResource\Pages;

use App\Filament\Resources\SupplierResource\SupplierResource;
use App\Filament\Resources\SupplierResource\Widgets\AllSuppliersChart;
use App\Filament\Resources\SupplierResource\Widgets\AllSuppliersStatsOverview;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSuppliers extends ListRecords
{
    protected static string $resource = SupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            AllSuppliersStatsOverview::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            AllSuppliersChart::class,
        ];
    }
}
