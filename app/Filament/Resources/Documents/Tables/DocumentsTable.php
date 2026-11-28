<?php

namespace App\Filament\Resources\Documents\Tables;

use App\Models\Document;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class DocumentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                #TextColumn::make('file_location')->searchable(),
                TextColumn::make('inventory_code')
                    ->searchable(),
                TextColumn::make('document_type')
                    ->searchable(),
                TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('file_size')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordUrl(fn (Document $record): string => route('documents.edit', $record))
            ->filters([
                SelectFilter::make('clients')
                    ->label('Client')
                    ->relationship('clients', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),

                SelectFilter::make('properties')
                    ->label('Property')
                    ->relationship('properties', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),

                SelectFilter::make('document_type')
                    ->options([
                        'land_title' => 'Land title',
                        'deed'       => 'Deed',
                        'contract'   => 'Contract',
                        'other'      => 'Other',
                    ]),

                // Optional: documents linked to anything of a given kind
                Filter::make('has_clients')
                    ->label('Linked to a client')
                    ->query(fn (Builder $query) => $query->whereHas('clients')),
                Filter::make('has_properties')
                    ->label('Linked to a property')
                    ->query(fn (Builder $query) => $query->whereHas('properties')),
            ])
            ->recordActions([
                Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => route('documents.view', $record))
                    ->openUrlInNewTab(),
                Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn ($record) => route('documents.download', $record)),
                Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn ($record) => route('documents.edit', $record)),
                        ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}