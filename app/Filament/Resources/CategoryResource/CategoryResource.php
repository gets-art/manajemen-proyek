<?php

namespace App\Filament\Resources\CategoryResource;

use App\Filament\NavigationGroup;
use App\Filament\Resources\CategoryResource\Pages\ListCategories;
use App\Filament\Resources\CategoryResource\Schemas\CategoryForm;
use App\Filament\Resources\CategoryResource\Schemas\CategoryInfolist;
use App\Filament\Resources\CategoryResource\Tables\CategoriesTable;
use App\Models\Category;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-squares-2x2';
    protected static string | \UnitEnum | null $navigationGroup = NavigationGroup::Catalog;
    protected static ?int $navigationSort = 1;
    public static function getModelLabel(): string { return __('app.resources.category.label'); }
    public static function getPluralModelLabel(): string { return __('app.resources.category.plural'); }

    public static function form(Schema $schema): Schema
    {
        return CategoryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CategoryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CategoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCategories::route('/'),
        ];
    }
}
