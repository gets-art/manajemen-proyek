<?php

namespace App\Filament\Resources\LanguageResource\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LanguageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app.sections.language_info'))
                    ->icon('heroicon-o-language')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->label(__('app.fields.name'))
                            ->required()
                            ->maxLength(255),

                        TextInput::make('symbol')
                            ->label(__('app.fields.symbol'))
                            ->required()
                            ->maxLength(10)
                            ->helperText(__('app.helpers.lang_symbol')),

                        Select::make('direction')
                            ->label(__('app.fields.direction'))
                            ->options([
                                'ltr' => __('app.directions.ltr'),
                                'rtl' => __('app.directions.rtl'),
                            ])
                            ->required()
                            ->default('ltr'),

                        FileUpload::make('image')
                            ->label(__('app.fields.flag_image'))
                            ->image()
                            ->directory('languages')
                            ->nullable(),

                        Toggle::make('active')
                            ->label(__('app.fields.active'))
                            ->default(true),
                    ]),
            ]);
    }
}
