<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use App\Filament\Resources\ProjectResource\Widgets\TasksStatsWidget;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\RenderHook;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\View\PanelsRenderHook;

class TasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getTabsContentComponent(),
                Livewire::make(TasksStatsWidget::class, ['projectId' => $this->getOwnerRecord()->getKey()]),
                RenderHook::make(PanelsRenderHook::RESOURCE_RELATION_MANAGER_BEFORE),
                EmbeddedTable::make(),
                RenderHook::make(PanelsRenderHook::RESOURCE_RELATION_MANAGER_AFTER),
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app.sections.task_details'))
                    ->icon('heroicon-o-clipboard-document-list')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('category.name')->label(__('app.fields.category'))->placeholder('—'),
                        TextEntry::make('description')->placeholder('—')->columnSpanFull(),
                        TextEntry::make('start_date')->label(__('app.fields.start_date'))->placeholder('—'),
                        TextEntry::make('end_date')->label(__('app.fields.end_date'))->placeholder('—'),
                    ]),
                Section::make(__('app.sections.financials'))
                    ->icon('heroicon-o-currency-dollar')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('final_total')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP')->label(__('app.fields.final_total')),
                        TextEntry::make('paid_total')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP')->label(__('app.fields.paid_total')),
                        TextEntry::make('rest_total')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP')->label(__('app.fields.rest_total')),
                    ]),
            ]);
    }

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
                TextColumn::make('category.name')->label(__('app.fields.category'))->placeholder('—'),
                TextColumn::make('final_total')
                    ->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP')
                    ->label(__('app.fields.final_total'))
                    ->summarize(Sum::make()->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP')->label(__('app.fields.total'))),
                TextColumn::make('paid_total')
                    ->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP')
                    ->label(__('app.fields.paid_total'))
                    ->summarize(Sum::make()->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP')->label(__('app.fields.total'))),
                TextColumn::make('rest_total')
                    ->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP')
                    ->label(__('app.fields.rest_total'))
                    ->summarize(Sum::make()->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP')->label(__('app.fields.total'))),
                TextColumn::make('start_date')->date()->label(__('app.fields.start_date')),
                TextColumn::make('end_date')->date()->label(__('app.fields.end_date')),
            ])
            ->recordActions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->headerActions([
                Actions\CreateAction::make()
                    ->form([
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
                    ]),
            ]);
    }
}
