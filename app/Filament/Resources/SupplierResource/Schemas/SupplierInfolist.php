<?php

namespace App\Filament\Resources\SupplierResource\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SupplierInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app.sections.supplier_details'))
                    ->icon('heroicon-o-truck')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        ImageEntry::make('image')->size(80),
                        TextEntry::make('name'),
                        TextEntry::make('phone'),
                        TextEntry::make('address')->placeholder('—'),
                        IconEntry::make('active')->boolean(),
                        TextEntry::make('purchase_tasks_count')->state(fn ($record) => $record->purchaseTasks()->count())->label(__('app.columns.purchases')),
                        TextEntry::make('payments_count')->state(fn ($record) => $record->payments()->count())->label(__('app.columns.payments')),
                        TextEntry::make('created_at')->dateTime(),
                    ]),
            ]);
    }
}
