<?php

namespace App\Filament\Resources\TaskResource\RelationManagers;

use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PurchaseTasksRelationManager extends RelationManager
{
    protected static string $relationship = 'purchaseTasks';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('supplier_id')
                    ->relationship('supplier', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('product_id')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                TextInput::make('quantity')
                    ->numeric()
                    ->default(1)
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        $qty = (float) ($get('quantity') ?? 0);
                        $unitPrice = (float) ($get('unit_price') ?? 0);
                        $discount = (float) ($get('discount') ?? 0);
                        $total = $qty * $unitPrice;
                        $set('total', $total);
                        $set('final_total', $total - $discount);
                    }),
                TextInput::make('unit_price')
                    ->numeric()
                    ->prefix('EGP')
                    ->default(0)
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        $qty = (float) ($get('quantity') ?? 0);
                        $unitPrice = (float) ($get('unit_price') ?? 0);
                        $discount = (float) ($get('discount') ?? 0);
                        $total = $qty * $unitPrice;
                        $set('total', $total);
                        $set('final_total', $total - $discount);
                    }),
                TextInput::make('total')
                    ->numeric()
                    ->prefix('EGP')
                    ->default(0)
                    ->readOnly(),
                TextInput::make('discount')
                    ->numeric()
                    ->prefix('EGP')
                    ->default(0)
                    ->reactive()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        $total = (float) ($get('total') ?? 0);
                        $discount = (float) ($get('discount') ?? 0);
                        $set('final_total', $total - $discount);
                    }),
                TextInput::make('final_total')
                    ->numeric()
                    ->prefix('EGP')
                    ->default(0)
                    ->readOnly(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['supplier', 'product']))
            ->columns([
                TextColumn::make('supplier.name')->label(__('app.fields.supplier'))->sortable(),
                TextColumn::make('product.name')->label(__('app.fields.product'))->placeholder('—'),
                TextColumn::make('quantity'),
                TextColumn::make('unit_price')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP')->label(__('app.fields.unit_price')),
                TextColumn::make('total')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP'),
                TextColumn::make('discount')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP'),
                TextColumn::make('final_total')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP')->label(__('app.fields.final')),
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
