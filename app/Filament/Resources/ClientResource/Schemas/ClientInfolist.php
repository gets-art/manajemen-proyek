<?php

namespace App\Filament\Resources\ClientResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClientInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app.sections.client_details'))
                    ->icon('heroicon-o-user-group')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('email')->placeholder('—'),
                        TextEntry::make('phone'),
                        TextEntry::make('address')->label('Alamat')->columnSpanFull()->placeholder('—'),
                        TextEntry::make('branch.name')->label('Cabang')->placeholder('—'),
                        TextEntry::make('notes')->columnSpanFull()->placeholder('—'),
                        TextEntry::make('projects_count')->state(fn ($record) => $record->projects()->count())->label(__('app.columns.projects')),
                        TextEntry::make('created_at')->dateTime(),
                    ]),
            ]);
    }
}
