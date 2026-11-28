<?php

namespace App\Filament\Resources\Properties\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PropertiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->withCount('documents'))
            ->columns([
                TextColumn::make('user.name')
                    ->label('Listed by')
                    ->placeholder('Unknown')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('type')
                    ->searchable(),
                TextColumn::make('size')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('price')
                    ->money('PHP')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'active' => 'success',
                        'sold'   => 'warning',
                        default  => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('postal')
                    ->searchable(),
                TextColumn::make('city')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordUrl(
                fn (Model $record): string => route('properties.show', $record),
                shouldOpenInNewTab: true,
            )
            ->filters([
                SelectFilter::make('status')->options([
                    'active'   => 'Active',
                    'sold'     => 'Sold',
                    'archived' => 'Archived',
                ]),
            ])
            ->recordActions([
                Action::make('documents')
                    ->label('Documents')
                    ->icon('heroicon-o-document-text')
                    ->badge(fn ($record) => $record->documents_count)
                    ->slideOver()
                    ->modalHeading(fn ($record) => 'Documents: ' . $record->name)
                    ->modalContent(fn ($record) => view('filament.property-documents', [
                        'property' => $record->load('documents'),
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}