<?php

namespace App\Filament\Resources\ClientResource\RelationManagers;

use App\Filament\Resources\ProjectResource\ProjectResource;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProjectsRelationManager extends RelationManager
{
    protected static string $relationship = 'projects';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app.sections.project_details'))
                    ->icon('heroicon-o-building-office-2')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('name')->label(__('app.fields.name')),
                        TextEntry::make('category.name')->label(__('app.fields.category'))->placeholder('—'),
                        TextEntry::make('status')
                            ->badge()
                            ->formatStateUsing(fn (int $state): string => match ($state) {
                                0 => __('app.statuses.pending'),
                                1 => __('app.statuses.in_progress'),
                                2 => __('app.statuses.completed'),
                                3 => __('app.statuses.cancelled'),
                                default => __('app.statuses.unknown'),
                            })
                            ->color(fn (int $state): string => match ($state) {
                                0 => 'warning',
                                1 => 'info',
                                2 => 'success',
                                3 => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('start_date')->label(__('app.fields.start_date'))->placeholder('—'),
                        TextEntry::make('end_date')->label(__('app.fields.end_date'))->placeholder('—'),
                        TextEntry::make('description')->label(__('app.fields.description'))->placeholder('—')->columnSpanFull(),
                    ]),
                Section::make(__('app.sections.financials'))
                    ->icon('heroicon-o-currency-dollar')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('final_total')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' IDR')->label(__('app.fields.final_total')),
                        TextEntry::make('paid_total')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' IDR')->label(__('app.fields.paid_total')),
                        TextEntry::make('rest_total')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' IDR')->label(__('app.fields.rest_total')),
                    ]),
            ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('app.fields.name'))
                    ->required()
                    ->maxLength(255),
                Select::make('category_id')
                    ->label(__('app.fields.category'))
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Select::make('status')
                    ->label(__('app.fields.status'))
                    ->options([
                        0 => __('app.statuses.pending'),
                        1 => __('app.statuses.in_progress'),
                        2 => __('app.statuses.completed'),
                        3 => __('app.statuses.cancelled'),
                    ])
                    ->default(0)
                    ->required(),
                DatePicker::make('start_date')
                    ->label(__('app.fields.start_date'))
                    ->native(false),
                DatePicker::make('end_date')
                    ->label(__('app.fields.end_date'))
                    ->native(false)
                    ->afterOrEqual('start_date'),
                TextInput::make('final_total')
                    ->label(__('app.fields.final_total'))
                    ->numeric()
                    ->prefix('IDR')
                    ->default(0),
                TextInput::make('paid_total')
                    ->label(__('app.fields.paid_total'))
                    ->numeric()
                    ->prefix('IDR')
                    ->default(0),
                TextInput::make('rest_total')
                    ->label(__('app.fields.rest_total'))
                    ->numeric()
                    ->prefix('IDR')
                    ->default(0),
                Textarea::make('description')
                    ->label(__('app.fields.description'))
                    ->rows(2)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('category'))
            ->columns([
                TextColumn::make('name')->searchable()->sortable()->limit(30),
                TextColumn::make('category.name')->label(__('app.fields.category'))->placeholder('—'),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        0 => __('app.statuses.pending'),
                        1 => __('app.statuses.in_progress'),
                        2 => __('app.statuses.completed'),
                        3 => __('app.statuses.cancelled'),
                        default => __('app.statuses.unknown'),
                    })
                    ->color(fn (int $state): string => match ($state) {
                        0 => 'warning',
                        1 => 'info',
                        2 => 'success',
                        3 => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('final_total')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' IDR')->label(__('app.columns.total')),
                TextColumn::make('paid_total')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' IDR')->label(__('app.columns.paid')),
                TextColumn::make('rest_total')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' IDR')->label(__('app.columns.rest')),
                TextColumn::make('start_date'),
            ])
            ->recordActions([
                Actions\ViewAction::make()
                    ->url(fn ($record) => ProjectResource::getUrl('view', ['record' => $record])),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->headerActions([
                Actions\CreateAction::make(),
            ]);
    }
}
