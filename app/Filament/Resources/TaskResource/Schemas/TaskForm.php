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
                            ->preload()
                            ->live(),

                        Select::make('project_budget_id')
                            ->label('Item RAB (Project Budget)')
                            ->relationship('projectBudget', 'name', fn ($query, $get) => $query->where('project_id', $get('project_id')))
                            ->searchable()
                            ->preload()
                            ->nullable(),

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
                        TextInput::make('contract_amount')
                            ->label('Nilai Kontrak Tukang')
                            ->helperText('Diisi jika task ini diborongkan (Upah Kesepakatan)')
                            ->numeric()
                            ->mask(\Filament\Support\RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->minValue(0)
                            ->prefix('IDR')
                            ->default(0),

                        TextInput::make('final_total')
                            ->label(__('app.fields.final_total'))
                            ->helperText('Otomatis disamakan dengan Nilai Kontrak jika kosong.')
                            ->numeric()
                            ->mask(\Filament\Support\RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->minValue(0)
                            ->prefix('IDR')
                            ->default(0),

                        TextInput::make('paid_total')
                            ->label(__('app.fields.paid_total'))
                            ->numeric()
                            ->mask(\Filament\Support\RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->minValue(0)
                            ->prefix('IDR')
                            ->default(0)
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Otomatis dihitung dari tabel Pembayaran.'),

                        TextInput::make('rest_total')
                            ->label(__('app.fields.rest_total'))
                            ->numeric()
                            ->mask(\Filament\Support\RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->minValue(0)
                            ->prefix('IDR')
                            ->default(0)
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Otomatis dihitung dari Nilai Kontrak - Sudah Dibayar.'),
                    ]),
            ]);
    }
}
