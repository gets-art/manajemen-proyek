<?php

namespace App\Filament\Resources\WorkerResource\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class WorkerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app.sections.worker_info'))
                    ->icon('heroicon-o-wrench-screwdriver')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->label(__('app.fields.name'))
                            ->required()
                            ->maxLength(255),

                        TextInput::make('phone_number')
                            ->label(__('app.fields.phone_number'))
                            ->required()
                            ->tel()
                            ->maxLength(255),

                        FileUpload::make('image')
                            ->label(__('app.fields.image'))
                            ->image()
                            ->directory('workers'),

                        Toggle::make('active')
                            ->label(__('app.fields.active'))
                            ->default(true),
                    ]),
            ]);
    }
}
