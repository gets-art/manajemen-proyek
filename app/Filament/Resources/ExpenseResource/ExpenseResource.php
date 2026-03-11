<?php

namespace App\Filament\Resources\ExpenseResource;

use App\Filament\NavigationGroup;
use App\Filament\Resources\ExpenseResource\Pages\ListExpenses;
use App\Filament\Resources\ExpenseResource\Schemas\ExpenseForm;
use App\Filament\Resources\ExpenseResource\Schemas\ExpenseInfolist;
use App\Filament\Resources\ExpenseResource\Tables\ExpensesTable;
use App\Models\Expense;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-receipt-percent';
    protected static string | \UnitEnum | null $navigationGroup = NavigationGroup::Finance;
    protected static ?int $navigationSort = 3;
    public static function getModelLabel(): string { return __('app.resources.expense.label'); }
    public static function getPluralModelLabel(): string { return __('app.resources.expense.plural'); }

    public static function form(Schema $schema): Schema
    {
        return ExpenseForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ExpenseInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExpensesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExpenses::route('/'),
        ];
    }
}
