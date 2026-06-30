<?php

namespace App\Filament\Resources\ProductResource\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Product Details')
                    ->icon('heroicon-o-cube')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        ImageEntry::make('image')->size(80),
                        TextEntry::make('name'),
                        TextEntry::make('description')->placeholder('—'),
                        TextEntry::make('price')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' IDR'),
                        TextEntry::make('category.name')->label('Category'),
                        IconEntry::make('active')->boolean(),
                        IconEntry::make('featured')->boolean(),
                        TextEntry::make('created_at')->dateTime(),
                    ]),
            ]);
    }
}
