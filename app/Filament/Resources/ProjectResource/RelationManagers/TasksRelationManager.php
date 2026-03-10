<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->rows(2),
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                DatePicker::make('start_date')
                    ->native(false),
                DatePicker::make('end_date')
                    ->native(false)
                    ->afterOrEqual('start_date'),
                TextInput::make('final_total')
                    ->numeric()
                    ->prefix('EGP')
                    ->default(0),
                TextInput::make('paid_total')
                    ->numeric()
                    ->prefix('EGP')
                    ->default(0),
                TextInput::make('rest_total')
                    ->numeric()
                    ->prefix('EGP')
                    ->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('category'))
            ->columns([
                TextColumn::make('name')->searchable()->sortable()->limit(30),
                TextColumn::make('category.name')->label('Category')->placeholder('—'),
                TextColumn::make('final_total')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP')->label('Total'),
                TextColumn::make('paid_total')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP')->label('Paid'),
                TextColumn::make('rest_total')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP')->label('Rest'),
                TextColumn::make('start_date'),
                TextColumn::make('end_date'),
            ])
            ->recordActions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->headerActions([
                Actions\CreateAction::make(),
            ]);
    }
}
