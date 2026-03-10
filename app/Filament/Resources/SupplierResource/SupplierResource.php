<?php

namespace App\Filament\Resources\SupplierResource;

use App\Filament\RelationManagers\PaymentsRelationManager;
use App\Filament\Resources\SupplierResource\Pages\CreateSupplier;
use App\Filament\Resources\SupplierResource\Pages\EditSupplier;
use App\Filament\Resources\SupplierResource\Pages\ListSuppliers;
use App\Filament\Resources\SupplierResource\Pages\ViewSupplier;
use App\Filament\Resources\SupplierResource\RelationManagers\PurchaseTasksRelationManager;
use App\Filament\Resources\SupplierResource\Schemas\SupplierForm;
use App\Filament\Resources\SupplierResource\Schemas\SupplierInfolist;
use App\Filament\Resources\SupplierResource\Tables\SuppliersTable;
use App\Filament\Resources\SupplierResource\Widgets\AllSuppliersChart;
use App\Filament\Resources\SupplierResource\Widgets\AllSuppliersStatsOverview;
use App\Filament\Resources\SupplierResource\Widgets\SupplierPurchasesChart;
use App\Filament\Resources\SupplierResource\Widgets\SupplierStatsOverview;
use App\Models\Supplier;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-truck';
    protected static string | \UnitEnum | null $navigationGroup = 'Procurement';
    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string { return __('app.nav_groups.procurement'); }
    public static function getModelLabel(): string { return __('app.resources.supplier.label'); }
    public static function getPluralModelLabel(): string { return __('app.resources.supplier.plural'); }

    public static function form(Schema $schema): Schema
    {
        return SupplierForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SupplierInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SuppliersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            PurchaseTasksRelationManager::class,
            PaymentsRelationManager::class,
        ];
    }

    public static function getWidgets(): array
    {
        return [
            SupplierStatsOverview::class,
            SupplierPurchasesChart::class,
            AllSuppliersStatsOverview::class,
            AllSuppliersChart::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSuppliers::route('/'),
            'create' => CreateSupplier::route('/create'),
            'view' => ViewSupplier::route('/{record}'),
            'edit' => EditSupplier::route('/{record}/edit'),
        ];
    }
}
