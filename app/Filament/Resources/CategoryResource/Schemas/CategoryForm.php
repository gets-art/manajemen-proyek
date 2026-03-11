<?php

namespace App\Filament\Resources\CategoryResource\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app.sections.category_info'))
                    ->icon('heroicon-o-squares-2x2')
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

                        Select::make('parent_id')
                            ->label(__('app.fields.parent_category'))
                            ->relationship('parent', 'name', fn ($query, $record) => $record ? $query->where('id', '!=', $record->id) : $query)
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        FileUpload::make('image')
                            ->label(__('app.fields.image'))
                            ->image()
                            ->directory('categories'),

                        Toggle::make('active')
                            ->label(__('app.fields.active'))
                            ->default(true),

                        Toggle::make('home_page')
                            ->label(__('app.fields.show_on_home'))
                            ->default(false),
                    ]),
            ]);
    }
}
