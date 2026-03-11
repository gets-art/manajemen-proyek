<?php

namespace App\Filament\Resources\ExpenseCategoryResource;

use App\Filament\NavigationGroup;
use App\Filament\Resources\ExpenseCategoryResource\Pages\ListExpenseCategories;
use App\Filament\Resources\ExpenseCategoryResource\Schemas\ExpenseCategoryForm;
use App\Filament\Resources\ExpenseCategoryResource\Schemas\ExpenseCategoryInfolist;
use App\Filament\Resources\ExpenseCategoryResource\Tables\ExpenseCategoriesTable;
use App\Models\ExpenseCategory;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ExpenseCategoryResource extends Resource
{
    protected static ?string $model = ExpenseCategory::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-tag';
    protected static string | \UnitEnum | null $navigationGroup = NavigationGroup::Finance;
    protected static ?int $navigationSort = 4;
    public static function getNavigationLabel(): string { return __('app.resources.expense_category.nav'); }
    public static function getModelLabel(): string { return __('app.resources.expense_category.label'); }
    public static function getPluralModelLabel(): string { return __('app.resources.expense_category.plural'); }

    public static function form(Schema $schema): Schema
    {
        return ExpenseCategoryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ExpenseCategoryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExpenseCategoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExpenseCategories::route('/'),
        ];
    }
}
