<?php

namespace App\Filament\Resources\PaymentResource\Schemas;

use App\Models\Project;
use App\Models\Supplier;
use App\Models\Worker;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app.sections.payment_info'))
                    ->icon('heroicon-o-banknotes')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        MorphToSelect::make('paymentable')
                            ->label(__('app.fields.pay_to'))
                            ->types([
                                MorphToSelect\Type::make(Project::class)
                                    ->titleAttribute('name'),
                                MorphToSelect\Type::make(Worker::class)
                                    ->titleAttribute('name'),
                                MorphToSelect\Type::make(Supplier::class)
                                    ->titleAttribute('name'),
                            ])
                            ->required()
                            ->searchable()
                            ->preload(),

                        Select::make('payment_method_id')
                            ->label(__('app.fields.payment_method'))
                            ->relationship('paymentMethod', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        TextInput::make('paid')
                            ->label(__('app.fields.amount'))
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->prefix('IDR'),

                        TextInput::make('payment_code')
                            ->label(__('app.fields.payment_code'))
                            ->maxLength(255),
                    ]),
            ]);
    }
}
