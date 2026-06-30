<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ProjectBudgetsRelationManager extends RelationManager
{
    protected static string $relationship = 'projectBudgets';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('unit')
                    ->required()
                    ->maxLength(255),
                TextInput::make('unit_price')
                    ->required()
                    ->numeric()
                    ->mask(\Filament\Support\RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->prefix('IDR'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric(),
                Tables\Columns\TextColumn::make('unit'),
                Tables\Columns\TextColumn::make('unit_price')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->money('IDR')
                    ->sortable()
                    ->summarize(Tables\Columns\Summarizers\Sum::make()->money('IDR')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                \Filament\Actions\CreateAction::make()
                    ->after(fn ($livewire) => $livewire->dispatch('refresh-form')),
                \Filament\Actions\Action::make('import')
                    ->label('Import Excel')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('success')
                    ->form([
                        FileUpload::make('file')
                            ->label('Excel File')
                            ->required()
                            ->acceptedFileTypes(['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'text/csv'])
                            ->storeFiles(false),
                    ])
                    ->action(function (array $data, RelationManager $livewire) {
                        $file = $data['file'];
                        if (!$file) return;

                        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
                        $worksheet = $spreadsheet->getActiveSheet();
                        $rows = $worksheet->toArray();
                        
                        // Skip header row
                        array_shift($rows);

                        foreach ($rows as $row) {
                            if (empty(array_filter($row))) continue;
                            
                            $livewire->getOwnerRecord()->projectBudgets()->create([
                                'name' => $row[0] ?? 'Item',
                                'description' => $row[1] ?? null,
                                'quantity' => (float) ($row[2] ?? 1),
                                'unit' => $row[3] ?? 'ls',
                                'unit_price' => (float) ($row[4] ?? 0),
                            ]);
                        }
                        
                        \Filament\Notifications\Notification::make()
                            ->title('RAB berhasil diimport')
                            ->success()
                            ->send();

                        $livewire->dispatch('refresh-form');
                    }),
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make()
                    ->after(fn ($livewire) => $livewire->dispatch('refresh-form')),
                \Filament\Actions\DeleteAction::make()
                    ->after(fn ($livewire) => $livewire->dispatch('refresh-form')),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make()
                        ->after(fn ($livewire) => $livewire->dispatch('refresh-form')),
                ]),
            ]);
    }
}
