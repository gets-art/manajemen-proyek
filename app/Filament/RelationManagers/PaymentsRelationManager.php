<?php

namespace App\Filament\RelationManagers;

use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app.sections.payment_details'))
                    ->icon('heroicon-o-banknotes')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('paymentMethod.name')->label(__('app.fields.payment_method')),
                        TextEntry::make('paid')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP')->label(__('app.fields.amount')),
                        TextEntry::make('payment_code')->label(__('app.fields.code'))->placeholder('—'),
                        TextEntry::make('created_at')->dateTime(),
                    ]),
            ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('payment_method_id')
                    ->relationship('paymentMethod', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('paid')
                    ->required()
                    ->numeric()
                    ->prefix('EGP')
                    ->label(__('app.fields.amount')),
                TextInput::make('payment_code')
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('paymentMethod'))
            ->columns([
                TextColumn::make('paymentMethod.name')->label(__('app.fields.method')),
                TextColumn::make('paid')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP')->label(__('app.fields.amount')),
                TextColumn::make('payment_code')->label(__('app.fields.code'))->placeholder('—'),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->recordActions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->headerActions([
                Actions\CreateAction::make(),
            ]);
    }
}
