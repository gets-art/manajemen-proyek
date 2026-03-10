<?php

namespace App\Filament\Resources\ExpenseCategoryResource\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ExpenseCategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app.sections.expense_category_details'))
                    ->icon('heroicon-o-tag')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('name'),
                        IconEntry::make('active')->boolean(),
                        TextEntry::make('expenses_count')->state(fn ($record) => $record->expenses()->count())->label(__('app.columns.expenses')),
                        TextEntry::make('created_at')->dateTime(),
                    ]),
            ]);
    }
}
