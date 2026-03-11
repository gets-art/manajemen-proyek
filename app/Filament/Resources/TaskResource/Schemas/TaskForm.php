<?php

namespace App\Filament\Resources\TaskResource\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TaskForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app.sections.task_details'))
                    ->icon('heroicon-o-clipboard-document-list')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->label(__('app.fields.name'))
                            ->required()
                            ->maxLength(255),

                        Select::make('project_id')
                            ->label(__('app.fields.project'))
                            ->relationship('project', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Select::make('category_id')
                            ->label(__('app.fields.category'))
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        DatePicker::make('start_date')
                            ->label(__('app.fields.start_date'))
                            ->native(false),

                        DatePicker::make('end_date')
                            ->label(__('app.fields.end_date'))
                            ->native(false)
                            ->afterOrEqual('start_date'),

                        Textarea::make('description')
                            ->label(__('app.fields.description'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make(__('app.sections.financials'))
                    ->icon('heroicon-o-currency-dollar')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('final_total')
                            ->label(__('app.fields.final_total'))
                            ->numeric()
                            ->minValue(0)
                            ->prefix('EGP')
                            ->default(0),

                        TextInput::make('paid_total')
                            ->label(__('app.fields.paid_total'))
                            ->numeric()
                            ->minValue(0)
                            ->prefix('EGP')
                            ->default(0),

                        TextInput::make('rest_total')
                            ->label(__('app.fields.rest_total'))
                            ->numeric()
                            ->minValue(0)
                            ->prefix('EGP')
                            ->default(0),
                    ]),
            ]);
    }
}
