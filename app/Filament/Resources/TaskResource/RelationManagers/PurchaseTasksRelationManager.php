<?php

namespace App\Filament\Resources\TaskResource\RelationManagers;

use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PurchaseTasksRelationManager extends RelationManager
{
    protected static string $relationship = 'purchaseTasks';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app.sections.purchase_details'))
                    ->icon('heroicon-o-shopping-cart')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('supplier.name')->label(__('app.fields.supplier')),
                        TextEntry::make('product.name')->label(__('app.fields.product'))->placeholder('—'),
                        TextEntry::make('quantity'),
                        TextEntry::make('unit_price')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' IDR')->label(__('app.fields.unit_price')),
                        TextEntry::make('total')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' IDR'),
                        TextEntry::make('discount')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' IDR'),
                        TextEntry::make('final_total')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' IDR')->label(__('app.fields.final')),
                    ]),
            ]);
    }

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
                    ->minValue(1)
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
                    ->minValue(0)
                    ->prefix('IDR')
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
                    ->prefix('IDR')
                    ->default(0)
                    ->readOnly(),
                TextInput::make('discount')
                    ->numeric()
                    ->minValue(0)
                    ->prefix('IDR')
                    ->default(0)
                    ->reactive()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        $total = (float) ($get('total') ?? 0);
                        $discount = (float) ($get('discount') ?? 0);
                        $set('final_total', $total - $discount);
                    }),
                TextInput::make('final_total')
                    ->numeric()
                    ->prefix('IDR')
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
                TextColumn::make('unit_price')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' IDR')->label(__('app.fields.unit_price')),
                TextColumn::make('total')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' IDR'),
                TextColumn::make('discount')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' IDR'),
                TextColumn::make('final_total')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' IDR')->label(__('app.fields.final')),
            ])
            ->recordActions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->headerActions([
                Actions\CreateAction::make(),
            ]);
    }
}
