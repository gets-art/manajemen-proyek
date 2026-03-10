<?php

namespace App\Filament\Resources\LanguageResource\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LanguageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app.sections.language_details'))
                    ->icon('heroicon-o-language')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        ImageEntry::make('image')->size(60),
                        TextEntry::make('name'),
                        TextEntry::make('symbol')->badge(),
                        TextEntry::make('direction')->badge()
                            ->color(fn (string $state): string => $state === 'rtl' ? 'warning' : 'info'),
                        IconEntry::make('active')->boolean(),
                        TextEntry::make('created_at')->dateTime(),
                    ]),
            ]);
    }
}
