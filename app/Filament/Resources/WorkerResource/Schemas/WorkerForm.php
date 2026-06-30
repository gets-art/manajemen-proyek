<?php

namespace App\Filament\Resources\WorkerResource\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
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

                        Select::make('type')
                            ->label(__('app.fields.worker_type'))
                            ->options([
                                'borongan' => __('app.fields.borongan'),
                                'harian' => __('app.fields.harian'),
                            ])
                            ->default('borongan')
                            ->required()
                            ->live(),

                        TextInput::make('daily_rate')
                            ->label(__('app.fields.daily_rate'))
                            ->numeric()
                            ->prefix('Rp')
                            ->visible(fn ($get) => $get('type') === 'harian'),

                        FileUpload::make('image')
                            ->label(__('app.fields.image'))
                            ->image()
                            ->directory('workers'),

                        Toggle::make('active')
                            ->label(__('app.fields.active'))
                            ->default(true),
                    ]),

                Section::make('Anggota Tim (SDM)')
                    ->icon('heroicon-o-users')
                    ->visible(fn ($get) => $get('type') === 'harian')
                    ->columnSpanFull()
                    ->schema([
                        \Filament\Forms\Components\Repeater::make('teamMembers')
                            ->relationship()
                            ->label('Daftar Tukang / Kenek')
                            ->columns(4)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama')
                                    ->required(),
                                Select::make('position')
                                    ->label('Jabatan')
                                    ->options([
                                        'Tukang' => 'Tukang',
                                        'Kenek/Helper' => 'Kenek/Helper',
                                    ])
                                    ->required(),
                                TextInput::make('daily_rate')
                                    ->label('Gaji Harian')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required(),
                                TextInput::make('phone_number')
                                    ->label('Nomor HP')
                                    ->tel(),
                                \Filament\Forms\Components\Hidden::make('type')
                                    ->default('harian'),
                                \Filament\Forms\Components\Hidden::make('active')
                                    ->default(true),
                            ])
                            ->addActionLabel('Tambah SDM (Tukang/Kenek)')
                            ->defaultItems(0),
                    ]),
            ]);
    }
}
