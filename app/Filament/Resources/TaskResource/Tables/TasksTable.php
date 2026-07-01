<?php

namespace App\Filament\Resources\TaskResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class TasksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['project', 'category', 'projectBudget']))
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('name')->searchable()->sortable()->limit(30),
                TextColumn::make('project.name')->label(__('app.fields.project'))->sortable()->limit(20),
                TextColumn::make('projectBudget.name')->label('Item RAB')->sortable()->limit(20),
                TextColumn::make('category.name')->label(__('app.fields.category'))->placeholder('—')->sortable(),
                TextColumn::make('contract_amount')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' IDR')->label('Nilai Kontrak')->sortable(),
                TextColumn::make('final_total')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' IDR')->label(__('app.columns.total'))->sortable(),
                TextColumn::make('paid_total')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' IDR')->label(__('app.columns.paid'))->sortable(),
                TextColumn::make('rest_total')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' IDR')->label('Sisa Tagihan (Tukang)')->sortable(),
                TextColumn::make('start_date')->date()->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('end_date')->date()->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('project_id')
                    ->relationship('project', 'name')
                    ->label(__('app.fields.project'))
                    ->searchable()
                    ->preload(),
                SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->label(__('app.fields.category')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->successNotificationTitle(__('app.notifications.deleted', ['resource' => __('app.resources.task.label')])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
