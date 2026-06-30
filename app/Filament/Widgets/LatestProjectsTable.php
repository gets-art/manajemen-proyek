<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestProjectsTable extends TableWidget
{
    protected static bool $isLazy = true;

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    public function getHeading(): ?string
    {
        return __('app.widgets.latest_projects');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Project::query()->with('client')->latest()->limit(5))
            ->columns([
                TextColumn::make('name')->sortable()->limit(30),
                TextColumn::make('client.name')->label(__('app.fields.client'))->placeholder('—'),
                TextColumn::make('status')
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
                TextColumn::make('final_total')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' IDR')->label(__('app.columns.total'))->color('primary')->weight('bold'),
                TextColumn::make('paid_total')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' IDR')->label(__('app.columns.paid'))->color('success'),
                TextColumn::make('created_at')->dateTime()->label(__('app.columns.created')),
            ])
            ->paginated(false);
    }
}
