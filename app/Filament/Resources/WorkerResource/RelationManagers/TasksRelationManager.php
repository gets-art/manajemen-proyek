<?php

namespace App\Filament\Resources\WorkerResource\RelationManagers;

use App\Filament\Resources\WorkerResource\Widgets\WorkerTasksStatsWidget;
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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\View\PanelsRenderHook;

class TasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

    protected static ?string $recordTitleAttribute = 'name';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getTabsContentComponent(),
                Livewire::make(WorkerTasksStatsWidget::class, ['workerId' => $this->getOwnerRecord()->getKey()]),
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
                        TextEntry::make('name')->label(__('app.columns.task')),
                        TextEntry::make('project.name')->label(__('app.fields.project'))->placeholder('—'),
                        TextEntry::make('category.name')->label(__('app.fields.category'))->placeholder('—'),
                        TextEntry::make('start_date')->label(__('app.columns.start'))->placeholder('—'),
                        TextEntry::make('end_date')->label(__('app.columns.end'))->placeholder('—'),
                    ]),
                Section::make(__('app.sections.financials'))
                    ->icon('heroicon-o-currency-dollar')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('final_total')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' IDR')->label(__('app.fields.final_total')),
                        TextEntry::make('paid_total')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' IDR')->label(__('app.fields.paid_total')),
                        TextEntry::make('rest_total')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' IDR')->label(__('app.fields.rest_total')),
                        TextEntry::make('pivot.paid')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' IDR')->label(__('app.fields.paid_amount')),
                    ]),
            ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('paid')
                    ->required()
                    ->numeric()
                    ->prefix('IDR')
                    ->default(0)
                    ->label(__('app.fields.paid_amount')),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['project', 'category']))
            ->columns([
                TextColumn::make('name')->label(__('app.columns.task'))->searchable(),
                TextColumn::make('project.name')->label(__('app.fields.project')),
                TextColumn::make('category.name')->label(__('app.fields.category')),
                TextColumn::make('pivot.paid')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' IDR')->label(__('app.columns.paid')),
                TextColumn::make('start_date')->date()->label(__('app.columns.start')),
                TextColumn::make('end_date')->date()->label(__('app.columns.end'))->placeholder('—'),
            ])
            ->recordActions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\DetachAction::make(),
            ])
            ->headerActions([
                Actions\CreateAction::make()
                    ->form([
                        Select::make('project_id')
                            ->relationship('project', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label(__('app.fields.project')),
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('description')
                            ->rows(2),
                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->label(__('app.fields.category')),
                        DatePicker::make('start_date')
                            ->label(__('app.fields.start_date'))
                            ->native(false),
                        DatePicker::make('end_date')
                            ->label(__('app.fields.end_date'))
                            ->native(false)
                            ->afterOrEqual('start_date'),
                        TextInput::make('final_total')
                            ->numeric()
                            ->prefix('IDR')
                            ->default(0)
                            ->label(__('app.fields.final_total')),
                        TextInput::make('paid_total')
                            ->numeric()
                            ->prefix('IDR')
                            ->default(0)
                            ->label(__('app.fields.paid_total')),
                        TextInput::make('rest_total')
                            ->numeric()
                            ->prefix('IDR')
                            ->default(0)
                            ->label(__('app.fields.rest_total')),
                    ]),
                Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->form(fn (Actions\AttachAction $action): array => [
                        $action->getRecordSelect(),
                        TextInput::make('paid')
                            ->required()
                            ->numeric()
                            ->prefix('IDR')
                            ->label(__('app.fields.paid_amount'))
                            ->default(0),
                    ]),
            ]);
    }
}
