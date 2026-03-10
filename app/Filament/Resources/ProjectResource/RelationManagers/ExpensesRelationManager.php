<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ExpensesRelationManager extends RelationManager
{
    protected static string $relationship = 'expenses';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->rows(2),
                TextInput::make('value')
                    ->required()
                    ->numeric()
                    ->prefix('EGP'),
                TextInput::make('date')
                    ->required()
                    ->maxLength(255),
                Select::make('expense_category_id')
                    ->relationship('expenseCategory', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('payment_method_id')
                    ->relationship('paymentMethod', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['expenseCategory', 'paymentMethod']))
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('value')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP')->sortable(),
                TextColumn::make('date'),
                TextColumn::make('expenseCategory.name')->label('Category'),
                TextColumn::make('paymentMethod.name')->label('Method')->placeholder('—'),
            ])
            ->recordActions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->headerActions([
                Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['added_by'] = Auth::id();
                        $data['last_edit_by'] = Auth::id();
                        return $data;
                    }),
            ]);
    }
}
