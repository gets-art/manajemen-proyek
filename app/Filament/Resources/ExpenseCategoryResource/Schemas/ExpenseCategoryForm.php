<?php

namespace App\Filament\Resources\ExpenseCategoryResource\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ExpenseCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app.sections.expense_category_info'))
                    ->icon('heroicon-o-tag')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->label(__('app.fields.name'))
                            ->required()
                            ->maxLength(255),

                        Toggle::make('active')
                            ->label(__('app.fields.active'))
                            ->default(true),
                    ]),
            ]);
    }
}
