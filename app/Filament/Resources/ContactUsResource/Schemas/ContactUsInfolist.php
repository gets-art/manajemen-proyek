<?php

namespace App\Filament\Resources\ContactUsResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContactUsInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app.sections.contact_message_details'))
                    ->icon('heroicon-o-envelope')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('email'),
                        TextEntry::make('phone')->placeholder('—'),
                        TextEntry::make('message')->columnSpanFull(),
                        TextEntry::make('created_at')->dateTime(),
                    ]),
            ]);
    }
}
