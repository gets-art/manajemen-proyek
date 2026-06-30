<?php

namespace App\Filament\Resources\Attendances\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;

class AttendancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')
                    ->label(__('app.fields.date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('project.name')
                    ->label(__('app.fields.project'))
                    ->sortable()
                    ->searchable(),
                ImageColumn::make('image')
                    ->label(__('app.fields.image_proof'))
                    ->url(fn ($record) => $record->image ? route('private.image', ['path' => $record->image]) : null)
                    ->openUrlInNewTab(),
                TextColumn::make('notes')
                    ->label('Catatan Harian')
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 30 ? $state : null;
                    }),
                TextColumn::make('user.name')
                    ->label(__('app.fields.by'))
                    ->sortable(),
                TextColumn::make('workers_count')
                    ->label(__('app.fields.workers'))
                    ->counts('workers'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
