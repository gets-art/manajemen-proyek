<?php

namespace App\Filament\Resources\PaymentResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PaymentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app.sections.payment_details'))
                    ->icon('heroicon-o-banknotes')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('paymentable_type')
                            ->label(__('app.fields.type'))
                            ->formatStateUsing(fn (string $state): string => class_basename($state))
                            ->badge()
                            ->color(fn (string $state): string => match (class_basename($state)) {
                                'Project' => 'success',
                                'Worker' => 'info',
                                'Supplier' => 'warning',
                                default => 'gray',
                            }),
                        TextEntry::make('paymentable.name')->label(__('app.fields.paid_to')),
                        TextEntry::make('paymentMethod.name')->label(__('app.fields.payment_method')),
                        TextEntry::make('paid')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' IDR')->label(__('app.fields.amount')),
                        TextEntry::make('payment_code')->label(__('app.fields.code'))->placeholder('—'),
                        TextEntry::make('created_at')->dateTime(),
                    ]),
            ]);
    }
}
