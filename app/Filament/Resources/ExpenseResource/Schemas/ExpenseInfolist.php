<?php

namespace App\Filament\Resources\ExpenseResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ExpenseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app.sections.expense_details'))
                    ->icon('heroicon-o-receipt-percent')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('value')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' IDR'),
                        TextEntry::make('date'),
                        TextEntry::make('expenseCategory.name')->label(__('app.fields.category')),
                        TextEntry::make('project.name')->label(__('app.fields.project'))->placeholder('—'),
                        TextEntry::make('paymentMethod.name')->label(__('app.fields.payment_method'))->placeholder('—'),
                        TextEntry::make('addedBy.name')->label(__('app.fields.added_by')),
                        TextEntry::make('description')->placeholder('—')->columnSpanFull(),
                        TextEntry::make('created_at')->dateTime(),
                    ]),
            ]);
    }
}
