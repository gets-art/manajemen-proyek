<?php

namespace App\Filament\Resources\ProductResource;

use App\Filament\NavigationGroup;
use App\Filament\Resources\ProductResource\Pages\ListProducts;
use App\Filament\Resources\ProductResource\Schemas\ProductForm;
use App\Filament\Resources\ProductResource\Schemas\ProductInfolist;
use App\Filament\Resources\ProductResource\Tables\ProductsTable;
use App\Models\Product;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cube';
    protected static string | \UnitEnum | null $navigationGroup = NavigationGroup::Catalog;
    protected static ?int $navigationSort = 2;
    public static function getModelLabel(): string { return __('app.resources.product.label'); }
    public static function getPluralModelLabel(): string { return __('app.resources.product.plural'); }

    public static function form(Schema $schema): Schema
    {
        return ProductForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProductInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
        ];
    }
}
