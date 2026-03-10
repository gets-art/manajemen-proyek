<?php

namespace App\Filament\Resources\PaymentMethodResource;

use App\Filament\Resources\PaymentMethodResource\Pages\CreatePaymentMethod;
use App\Filament\Resources\PaymentMethodResource\Pages\EditPaymentMethod;
use App\Filament\Resources\PaymentMethodResource\Pages\ListPaymentMethods;
use App\Filament\Resources\PaymentMethodResource\Pages\ViewPaymentMethod;
use App\Filament\Resources\PaymentMethodResource\Schemas\PaymentMethodForm;
use App\Filament\Resources\PaymentMethodResource\Schemas\PaymentMethodInfolist;
use App\Filament\Resources\PaymentMethodResource\Tables\PaymentMethodsTable;
use App\Models\PaymentMethod;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class PaymentMethodResource extends Resource
{
    protected static ?string $model = PaymentMethod::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-credit-card';
    protected static string | \UnitEnum | null $navigationGroup = 'Finance';
    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string { return __('app.nav_groups.finance'); }
    public static function getModelLabel(): string { return __('app.resources.payment_method.label'); }
    public static function getPluralModelLabel(): string { return __('app.resources.payment_method.plural'); }

    public static function form(Schema $schema): Schema
    {
        return PaymentMethodForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PaymentMethodInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PaymentMethodsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPaymentMethods::route('/'),
            'create' => CreatePaymentMethod::route('/create'),
            'view' => ViewPaymentMethod::route('/{record}'),
            'edit' => EditPaymentMethod::route('/{record}/edit'),
        ];
    }
}
