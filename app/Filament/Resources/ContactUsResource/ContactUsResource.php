<?php

namespace App\Filament\Resources\ContactUsResource;

use App\Filament\NavigationGroup;
use App\Filament\Resources\ContactUsResource\Pages\ListContactUs;
use App\Filament\Resources\ContactUsResource\Pages\ViewContactUs;
use App\Filament\Resources\ContactUsResource\Schemas\ContactUsInfolist;
use App\Filament\Resources\ContactUsResource\Tables\ContactUsTable;
use App\Models\ContactUs;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ContactUsResource extends Resource
{
    protected static ?string $model = ContactUs::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-envelope';
    protected static string | \UnitEnum | null $navigationGroup = NavigationGroup::CMS;
    protected static ?int $navigationSort = 3;
    public static function getNavigationLabel(): string { return __('app.resources.contact_message.nav'); }
    public static function getModelLabel(): string { return __('app.resources.contact_message.label'); }
    public static function getPluralModelLabel(): string { return __('app.resources.contact_message.plural'); }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function infolist(Schema $schema): Schema
    {
        return ContactUsInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContactUsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContactUs::route('/'),
            'view' => ViewContactUs::route('/{record}'),
        ];
    }
}
