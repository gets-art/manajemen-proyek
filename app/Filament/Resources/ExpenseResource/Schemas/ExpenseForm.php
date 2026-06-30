<?php

namespace App\Filament\Resources\ExpenseResource\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ExpenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app.sections.expense_info'))
                    ->icon('heroicon-o-receipt-percent')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->label(__('app.fields.name'))
                            ->required()
                            ->maxLength(255),

                        TextInput::make('value')
                            ->label(__('app.fields.value'))
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->prefix('IDR'),

                        DatePicker::make('date')
                            ->label(__('app.fields.date'))
                            ->required()
                            ->native(false),

                        Select::make('expense_category_id')
                            ->label(__('app.fields.expense_category'))
                            ->relationship('expenseCategory', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Select::make('project_id')
                            ->label(__('app.fields.project'))
                            ->relationship('project', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Select::make('payment_method_id')
                            ->label(__('app.fields.payment_method'))
                            ->relationship('paymentMethod', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Textarea::make('description')
                            ->label(__('app.fields.description'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
