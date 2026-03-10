<?php

namespace App\Filament\Resources\WorkerResource\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class WorkerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app.sections.worker_details'))
                    ->icon('heroicon-o-wrench-screwdriver')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        ImageEntry::make('image')->size(80),
                        TextEntry::make('name'),
                        TextEntry::make('phone_number')->label(__('app.fields.phone')),
                        IconEntry::make('active')->boolean(),
                        TextEntry::make('tasks_count')->state(fn ($record) => $record->tasks()->count())->label(__('app.columns.tasks')),
                        TextEntry::make('created_at')->dateTime(),
                    ]),
            ]);
    }
}
