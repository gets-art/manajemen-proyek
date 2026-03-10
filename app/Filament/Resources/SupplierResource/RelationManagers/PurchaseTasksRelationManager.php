<?php

namespace App\Filament\Resources\SupplierResource\RelationManagers;

use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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
                Select::make('task_id')
                    ->relationship('task', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('product_id')
                    ->relationship('product', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->live()
                    ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::updateTotals($set, $get)),
                TextInput::make('unit_price')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->live()
                    ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::updateTotals($set, $get)),
                TextInput::make('total')
                    ->numeric()
                    ->readOnly(),
                TextInput::make('discount')
                    ->numeric()
                    ->default(0)
                    ->live()
                    ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::updateTotals($set, $get)),
                TextInput::make('final_total')
                    ->numeric()
                    ->readOnly(),
            ]);
    }

    protected static function updateTotals(callable $set, callable $get): void
    {
        $qty = (float) ($get('quantity') ?? 0);
        $unitPrice = (float) ($get('unit_price') ?? 0);
        $discount = (float) ($get('discount') ?? 0);
        $total = $qty * $unitPrice;
        $set('total', $total);
        $set('final_total', $total - $discount);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['task.project', 'product']))
            ->columns([
                TextColumn::make('task.project.name')->label(__('app.fields.project'))->searchable(),
                TextColumn::make('task.name')->label('Task')->searchable(),
                TextColumn::make('product.name')->label('Product'),
                TextColumn::make('quantity'),
                TextColumn::make('unit_price')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP'),
                TextColumn::make('total')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP'),
                TextColumn::make('discount')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP'),
                TextColumn::make('final_total')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP'),
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
