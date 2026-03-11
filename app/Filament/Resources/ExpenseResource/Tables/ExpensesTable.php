<?php

namespace App\Filament\Resources\ExpenseResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ExpensesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['expenseCategory', 'project', 'paymentMethod', 'addedBy']))
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('value')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP')->sortable(),
                TextColumn::make('date')->date()->sortable(),
                TextColumn::make('expenseCategory.name')->label(__('app.fields.category'))->sortable(),
                TextColumn::make('project.name')->label(__('app.fields.project'))->placeholder('—')->sortable(),
                TextColumn::make('paymentMethod.name')->label(__('app.fields.method'))->placeholder('—')->sortable(),
                TextColumn::make('addedBy.name')->label(__('app.fields.added_by')),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('expense_category_id')
                    ->relationship('expenseCategory', 'name')
                    ->label(__('app.fields.category')),
                SelectFilter::make('project_id')
                    ->relationship('project', 'name')
                    ->label(__('app.fields.project')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->successNotificationTitle(__('app.notifications.deleted', ['resource' => __('app.resources.expense.label')])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
