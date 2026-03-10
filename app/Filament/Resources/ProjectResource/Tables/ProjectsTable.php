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
                TextColumn::make('client.name')->label('Client')->placeholder('—'),
                TextColumn::make('category.name')->label('Category')->placeholder('—'),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        0 => 'Pending',
                        1 => 'In Progress',
                        2 => 'Completed',
                        3 => 'Cancelled',
                        default => 'Unknown',
                    })
                    ->color(fn (int $state): string => match ($state) {
                        0 => 'warning',
                        1 => 'info',
                        2 => 'success',
                        3 => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('final_total')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP')->label('Total'),
                TextColumn::make('paid_total')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP')->label('Paid'),
                TextColumn::make('rest_total')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP')->label('Rest'),
                TextColumn::make('start_date')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('status')
                    ->options([
                        0 => 'Pending',
                        1 => 'In Progress',
                        2 => 'Completed',
                        3 => 'Cancelled',
                    ]),
                SelectFilter::make('client_id')
                    ->relationship('client', 'name')
                    ->label('Client'),
                SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->label('Category'),
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
