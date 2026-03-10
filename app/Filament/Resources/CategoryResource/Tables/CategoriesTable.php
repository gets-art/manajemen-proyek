<?php

namespace App\Filament\Resources\CategoryResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('parent'))
            ->columns([
                TextColumn::make('id')->sortable(),
                ImageColumn::make('image')->circular(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('parent.name')->label(__('app.columns.parent'))->placeholder('—'),
                IconColumn::make('active')->boolean(),
                IconColumn::make('home_page')->boolean()->label(__('app.columns.home')),
                TextColumn::make('products_count')->counts('products')->label(__('app.columns.products')),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('parent_id')
                    ->relationship('parent', 'name')
                    ->label(__('app.columns.parent')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->successNotificationTitle(__('app.notifications.deleted', ['resource' => __('app.resources.category.label')])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
