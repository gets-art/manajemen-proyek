<?php

namespace App\Filament\Resources\ProjectResource\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProjectInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app.sections.basic_info'))
                    ->icon('heroicon-o-building-office-2')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        ImageEntry::make('image')->size(120),
                        TextEntry::make('name'),
                        TextEntry::make('description')->placeholder('—'),
                        TextEntry::make('client.name')->label(__('app.fields.client'))->placeholder('—'),
                        TextEntry::make('category.name')->label(__('app.fields.category'))->placeholder('—'),
                        TextEntry::make('status')
                            ->badge()
                            ->formatStateUsing(fn (int $state): string => match ($state) {
                                0 => __('app.statuses.pending'),
                                1 => __('app.statuses.in_progress'),
                                2 => __('app.statuses.completed'),
                                3 => __('app.statuses.cancelled'),
                                default => __('app.statuses.unknown'),
                            })
                            ->color(fn (int $state): string => match ($state) {
                                0 => 'warning',
                                1 => 'info',
                                2 => 'success',
                                3 => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('start_date')->placeholder('—'),
                        TextEntry::make('end_date')->placeholder('—'),
                    ]),

                Section::make(__('app.sections.financials'))
                    ->icon('heroicon-o-currency-dollar')
                    ->columns(4)
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('final_total')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP'),
                        TextEntry::make('paid_total')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP'),
                        TextEntry::make('rest_total')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP'),
                        TextEntry::make('observation')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP'),
                    ]),

                Section::make(__('app.sections.notes'))
                    ->icon('heroicon-o-document-text')
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('note')->placeholder('—'),
                    ]),
            ]);
    }
}
