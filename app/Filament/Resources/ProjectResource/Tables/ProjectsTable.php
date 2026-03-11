<?php

namespace App\Filament\Resources\ProjectResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['client', 'category']))
            ->columns([
                TextColumn::make('id')->sortable(),
                ImageColumn::make('image')->size(40)->circular(),
                TextColumn::make('name')->searchable()->sortable()->limit(30),
                TextColumn::make('client.name')->label(__('app.fields.client'))->placeholder('—'),
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
                TextColumn::make('final_total')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP')->label(__('app.columns.total'))->sortable(),
                TextColumn::make('paid_total')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP')->label(__('app.columns.paid'))->sortable(),
                TextColumn::make('rest_total')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP')->label(__('app.columns.rest'))->sortable(),
                TextColumn::make('start_date')->date()->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('status')
                    ->options([
                        0 => __('app.statuses.pending'),
                        1 => __('app.statuses.in_progress'),
                        2 => __('app.statuses.completed'),
                        3 => __('app.statuses.cancelled'),
                    ]),
                SelectFilter::make('client_id')
                    ->relationship('client', 'name')
                    ->label(__('app.fields.client')),
                SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->label(__('app.fields.category')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->successNotificationTitle(__('app.notifications.deleted', ['resource' => __('app.resources.project.label')])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
