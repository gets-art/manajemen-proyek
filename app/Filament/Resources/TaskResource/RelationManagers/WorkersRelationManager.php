<?php

namespace App\Filament\Resources\TaskResource\RelationManagers;

use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WorkersRelationManager extends RelationManager
{
    protected static string $relationship = 'workers';

    protected static ?string $recordTitleAttribute = 'name';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app.sections.worker_details'))
                    ->icon('heroicon-o-wrench-screwdriver')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('name')->label(__('app.fields.name')),
                        TextEntry::make('phone_number')->label(__('app.fields.phone'))->placeholder('—'),
                        TextEntry::make('paid')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP')->label(__('app.fields.paid_amount')),
                    ]),
            ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('paid')
                    ->numeric()
                    ->prefix('EGP')
                    ->default(0)
                    ->label(__('app.fields.paid_amount')),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('phone_number')->label(__('app.fields.phone')),
                TextColumn::make('paid')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP')->label(__('app.columns.paid')),
            ])
            ->recordActions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\DetachAction::make(),
            ])
            ->headerActions([
                Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->form(fn (Actions\AttachAction $action): array => [
                        $action->getRecordSelect(),
                        TextInput::make('paid')
                            ->numeric()
                            ->prefix('EGP')
                            ->default(0)
                            ->label(__('app.fields.paid_amount')),
                    ]),
            ]);
    }
}
