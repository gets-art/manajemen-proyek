<?php

namespace App\Filament\Resources\LanguageResource;

use App\Filament\Resources\LanguageResource\Pages\CreateLanguage;
use App\Filament\Resources\LanguageResource\Pages\EditLanguage;
use App\Filament\Resources\LanguageResource\Pages\ListLanguages;
use App\Filament\Resources\LanguageResource\Pages\ViewLanguage;
use App\Filament\Resources\LanguageResource\Schemas\LanguageForm;
use App\Filament\Resources\LanguageResource\Schemas\LanguageInfolist;
use App\Filament\Resources\LanguageResource\Tables\LanguagesTable;
use App\Models\Language;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class LanguageResource extends Resource
{
    protected static ?string $model = Language::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-language';
    protected static string | \UnitEnum | null $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string { return __('app.nav_groups.settings'); }
    public static function getModelLabel(): string { return __('app.resources.language.label'); }
    public static function getPluralModelLabel(): string { return __('app.resources.language.plural'); }

    public static function form(Schema $schema): Schema
    {
        return LanguageForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LanguageInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LanguagesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLanguages::route('/'),
            'create' => CreateLanguage::route('/create'),
            'view' => ViewLanguage::route('/{record}'),
            'edit' => EditLanguage::route('/{record}/edit'),
        ];
    }
}
