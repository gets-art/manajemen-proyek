<?php

namespace App\Filament\Resources\TaskResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TaskInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app.sections.task_details'))
                    ->icon('heroicon-o-clipboard-document-list')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('project.name')->label(__('app.fields.project')),
                        TextEntry::make('category.name')->label(__('app.fields.category'))->placeholder('—'),
                        TextEntry::make('start_date')->placeholder('—'),
                        TextEntry::make('end_date')->placeholder('—'),
                        TextEntry::make('description')->placeholder('—')->columnSpanFull(),
                    ]),

                Section::make(__('app.sections.financials'))
                    ->icon('heroicon-o-currency-dollar')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('final_total')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP')->label(__('app.columns.total')),
                        TextEntry::make('paid_total')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP')->label(__('app.columns.paid')),
                        TextEntry::make('rest_total')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP')->label(__('app.columns.rest')),
                    ]),

                Section::make(__('app.sections.metadata'))
                    ->icon('heroicon-o-clock')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('created_at')->dateTime(),
                        TextEntry::make('updated_at')->dateTime(),
                    ]),
            ]);
    }
}
