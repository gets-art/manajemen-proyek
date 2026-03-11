<?php

namespace App\Filament\Resources\SupplierResource\RelationManagers;

use App\Filament\Resources\SupplierResource\Widgets\PurchaseTasksStatsWidget;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\RenderHook;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\View\PanelsRenderHook;

class PurchaseTasksRelationManager extends RelationManager
{
    protected static string $relationship = 'purchaseTasks';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getTabsContentComponent(),
                Livewire::make(PurchaseTasksStatsWidget::class, ['supplierId' => $this->getOwnerRecord()->getKey()]),
                RenderHook::make(PanelsRenderHook::RESOURCE_RELATION_MANAGER_BEFORE),
                EmbeddedTable::make(),
                RenderHook::make(PanelsRenderHook::RESOURCE_RELATION_MANAGER_AFTER),
            ]);
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
                        TextEntry::make('task.project.name')->label(__('app.fields.project')),
                        TextEntry::make('task.name')->label(__('app.columns.task')),
                        TextEntry::make('product.name')->label(__('app.fields.product'))->placeholder('—'),
                        TextEntry::make('quantity'),
                        TextEntry::make('unit_price')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP'),
                        TextEntry::make('total')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP'),
                        TextEntry::make('discount')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP'),
                        TextEntry::make('final_total')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP'),
                    ]),
            ]);
    }

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
                    ->minValue(1)
                    ->default(1)
                    ->live()
                    ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::updateTotals($set, $get)),
                TextInput::make('unit_price')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->prefix('EGP')
                    ->live()
                    ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::updateTotals($set, $get)),
                TextInput::make('total')
                    ->numeric()
                    ->prefix('EGP')
                    ->readOnly(),
                TextInput::make('discount')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->prefix('EGP')
                    ->live()
                    ->afterStateUpdated(fn ($state, callable $set, callable $get) => self::updateTotals($set, $get)),
                TextInput::make('final_total')
                    ->numeric()
                    ->prefix('EGP')
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
                TextColumn::make('task.name')->label(__('app.columns.task'))->searchable(),
                TextColumn::make('product.name')->label(__('app.fields.product')),
                TextColumn::make('quantity'),
                TextColumn::make('unit_price')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP'),
                TextColumn::make('total')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP'),
                TextColumn::make('discount')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP'),
                TextColumn::make('final_total')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP'),
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
