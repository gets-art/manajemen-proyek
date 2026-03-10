<?php

namespace App\Filament\Resources\ProductResource\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app.sections.product_info'))
                    ->icon('heroicon-o-cube')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->label(__('app.fields.name'))
                            ->required()
                            ->maxLength(255),

                        TextInput::make('description')
                            ->label(__('app.fields.description'))
                            ->maxLength(500),

                        TextInput::make('price')
                            ->label(__('app.fields.price'))
                            ->required()
                            ->numeric()
                            ->prefix('EGP'),

                        Select::make('category_id')
                            ->label(__('app.fields.category'))
                            ->relationship('category', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        FileUpload::make('image')
                            ->label(__('app.fields.image'))
                            ->image()
                            ->directory('products'),

                        Toggle::make('active')
                            ->label(__('app.fields.active'))
                            ->default(true),

                        Toggle::make('featured')
                            ->label(__('app.fields.featured'))
                            ->default(false),
                    ]),
            ]);
    }
}
