<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ExpensesRelationManager extends RelationManager
{
    protected static string $relationship = 'expenses';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app.sections.expense_details'))
                    ->icon('heroicon-o-receipt-percent')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('value')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP'),
                        TextEntry::make('date')->date(),
                        TextEntry::make('expenseCategory.name')->label(__('app.fields.expense_category')),
                        TextEntry::make('paymentMethod.name')->label(__('app.fields.payment_method'))->placeholder('—'),
                        TextEntry::make('description')->placeholder('—')->columnSpanFull(),
                        TextEntry::make('created_at')->dateTime(),
                    ]),
            ]);
    }

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
                    ->minValue(0)
                    ->prefix('EGP'),
                DatePicker::make('date')
                    ->required()
                    ->native(false),
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
                TextColumn::make('date')->date()->sortable(),
                TextColumn::make('expenseCategory.name')->label(__('app.fields.expense_category')),
                TextColumn::make('paymentMethod.name')->label(__('app.fields.payment_method'))->placeholder('—'),
            ])
            ->recordActions([
                Actions\ViewAction::make(),
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
