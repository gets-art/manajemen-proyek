<?php

namespace App\Filament\Resources\ProjectResource\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app.sections.basic_info'))
                    ->icon('heroicon-o-building-office-2')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->label(__('app.fields.name'))
                            ->required()
                            ->maxLength(255),

                        Select::make('client_id')
                            ->label(__('app.fields.client'))
                            ->relationship('client', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Select::make('category_id')
                            ->label(__('app.fields.category'))
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Select::make('status')
                            ->label(__('app.fields.status'))
                            ->options([
                                0 => __('app.statuses.pending'),
                                1 => __('app.statuses.in_progress'),
                                2 => __('app.statuses.completed'),
                                3 => __('app.statuses.cancelled'),
                            ])
                            ->default(0)
                            ->required(),

                        DatePicker::make('start_date')
                            ->label(__('app.fields.start_date'))
                            ->native(false),

                        DatePicker::make('end_date')
                            ->label(__('app.fields.end_date'))
                            ->native(false)
                            ->afterOrEqual('start_date'),

                        FileUpload::make('image')
                            ->label(__('app.fields.image'))
                            ->image()
                            ->directory('projects')
                            ->nullable(),

                        Textarea::make('description')
                            ->label(__('app.fields.description'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make(__('app.sections.financials'))
                    ->icon('heroicon-o-currency-dollar')
                    ->columns(4)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('final_total')
                            ->label(__('app.fields.final_total'))
                            ->numeric()
                            ->prefix('EGP')
                            ->default(0),

                        TextInput::make('paid_total')
                            ->label(__('app.fields.paid_total'))
                            ->numeric()
                            ->prefix('EGP')
                            ->default(0),

                        TextInput::make('rest_total')
                            ->label(__('app.fields.rest_total'))
                            ->numeric()
                            ->prefix('EGP')
                            ->default(0),

                        TextInput::make('observation')
                            ->label(__('app.fields.observation'))
                            ->numeric()
                            ->prefix('EGP')
                            ->default(0),
                    ]),

                Section::make(__('app.sections.notes'))
                    ->icon('heroicon-o-document-text')
                    ->columnSpanFull()
                    ->schema([
                        Textarea::make('note')
                            ->label(__('app.fields.notes'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
