<?php

namespace App\Filament\Resources\SupplierResource\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SupplierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app.sections.supplier_info'))
                    ->icon('heroicon-o-truck')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->label(__('app.fields.name'))
                            ->required()
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->label(__('app.fields.phone'))
                            ->required()
                            ->tel()
                            ->maxLength(255),

                        TextInput::make('address')
                            ->label(__('app.fields.address'))
                            ->maxLength(255),

                        FileUpload::make('image')
                            ->label(__('app.fields.image'))
                            ->image()
                            ->directory('suppliers'),

                        Toggle::make('active')
                            ->label(__('app.fields.active'))
                            ->default(true),
                    ]),
            ]);
    }
}
