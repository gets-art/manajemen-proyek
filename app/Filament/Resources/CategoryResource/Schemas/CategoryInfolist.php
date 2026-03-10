<?php

namespace App\Filament\Resources\CategoryResource\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app.sections.category_details'))
                    ->icon('heroicon-o-squares-2x2')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        ImageEntry::make('image')->size(80),
                        TextEntry::make('name'),
                        TextEntry::make('description')->placeholder('—'),
                        TextEntry::make('parent.name')->label(__('app.fields.parent_category'))->placeholder('—'),
                        IconEntry::make('active')->boolean(),
                        IconEntry::make('home_page')->boolean()->label(__('app.fields.show_on_home')),
                        TextEntry::make('products_count')->state(fn ($record) => $record->products()->count())->label(__('app.columns.products')),
                        TextEntry::make('created_at')->dateTime(),
                    ]),
            ]);
    }
}
