<?php

namespace App\Filament\Resources\WorkerResource\RelationManagers;

use Filament\Actions;
use Filament\Forms\Components\Select;
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
                Select::make('task_id')
                    ->relationship('tasks', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('paid')
                    ->required()
                    ->numeric()
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
                TextColumn::make('pivot.paid')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP')->label(__('app.columns.paid')),
                TextColumn::make('start_date')->label(__('app.columns.start')),
                TextColumn::make('end_date')->label(__('app.columns.end'))->placeholder('—'),
            ])
            ->recordActions([
                Actions\EditAction::make(),
                Actions\DetachAction::make(),
            ])
            ->headerActions([
                Actions\AttachAction::make()
                    ->form(fn (Actions\AttachAction $action): array => [
                        $action->getRecordSelect(),
                        TextInput::make('paid')
                            ->required()
                            ->numeric()
                            ->label(__('app.fields.paid_amount'))
                            ->default(0),
                    ]),
            ]);
    }
}
