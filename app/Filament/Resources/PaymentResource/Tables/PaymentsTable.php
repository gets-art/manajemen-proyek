<?php

namespace App\Filament\Resources\PaymentResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['paymentable', 'paymentMethod']))
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('paymentable_type')
                    ->label(__('app.fields.type'))
                    ->formatStateUsing(fn (string $state): string => class_basename($state)),
                TextColumn::make('paymentable.name')->label(__('app.fields.paid_to'))->searchable(),
                TextColumn::make('paymentMethod.name')->label(__('app.fields.method'))->sortable(),
                TextColumn::make('paid')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' IDR')->label(__('app.fields.amount'))->sortable(),
                TextColumn::make('payment_code')->label(__('app.fields.code'))->placeholder('—')->searchable(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('payment_method_id')
                    ->relationship('paymentMethod', 'name')
                    ->label(__('app.fields.method')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->successNotificationTitle(__('app.notifications.deleted', ['resource' => __('app.resources.payment.label')])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
