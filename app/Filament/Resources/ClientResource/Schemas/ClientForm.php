<?php

namespace App\Filament\Resources\ClientResource\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app.sections.client_info'))
                    ->icon('heroicon-o-user-group')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->label(__('app.fields.name'))
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label(__('app.fields.email'))
                            ->email()
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->label(__('app.fields.phone'))
                            ->required()
                            ->tel()
                            ->maxLength(255),

                        Textarea::make('address')
                            ->label('Alamat')
                            ->rows(3)
                            ->columnSpanFull(),

                        \Filament\Forms\Components\Select::make('branch_id')
                            ->label('Cabang')
                            ->relationship('branch', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                \Filament\Forms\Components\TextInput::make('name')
                                    ->label('Nama Cabang')
                                    ->required()
                                    ->maxLength(255),
                                \Filament\Forms\Components\Textarea::make('address')
                                    ->label('Alamat Cabang')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ])
                            ->columnSpanFull(),

                        Textarea::make('notes')
                            ->label(__('app.fields.notes'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
