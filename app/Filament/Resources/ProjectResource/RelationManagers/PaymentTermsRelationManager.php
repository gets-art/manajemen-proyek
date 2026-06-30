<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentTermsRelationManager extends RelationManager
{
    protected static string $relationship = 'paymentTerms';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('percentage')
                    ->required()
                    ->numeric()
                    ->suffix('%')
                    ->maxValue(100),
                TextInput::make('target_progress_percentage')
                    ->numeric()
                    ->suffix('%')
                    ->maxValue(100)
                    ->label('Target Progress (%)'),
                Select::make('status')
                    ->options([
                        'Pending' => 'Pending',
                        'Invoiced' => 'Invoiced',
                        'Paid' => 'Paid',
                    ])
                    ->default('Pending')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('percentage')
                    ->numeric()
                    ->suffix('%')
                    ->summarize(Tables\Columns\Summarizers\Sum::make()->label('Total %')),
                Tables\Columns\TextColumn::make('amount')
                    ->money('IDR')
                    ->sortable()
                    ->summarize(Tables\Columns\Summarizers\Sum::make()->money('IDR')),
                Tables\Columns\TextColumn::make('target_progress_percentage')
                    ->numeric()
                    ->suffix('%')
                    ->label('Target Progress'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pending' => 'gray',
                        'Invoiced' => 'warning',
                        'Paid' => 'success',
                        default => 'gray',
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                \Filament\Actions\CreateAction::make(),
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
