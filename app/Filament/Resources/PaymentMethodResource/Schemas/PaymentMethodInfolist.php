<?php

namespace App\Filament\Resources\PaymentMethodResource\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PaymentMethodInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app.sections.payment_method_details'))
                    ->icon('heroicon-o-credit-card')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        ImageEntry::make('image')->size(80),
                        TextEntry::make('name'),
                        IconEntry::make('active')->boolean(),
                        TextEntry::make('payments_count')->state(fn ($record) => $record->payments()->count())->label(__('app.columns.payments')),
                        TextEntry::make('created_at')->dateTime(),
                    ]),
            ]);
    }
}
