<?php

namespace App\Filament\Resources\Documents\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->required()
                ->maxLength(255),

            TextInput::make('inventory_code')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true)
                ->validationMessages(['unique' => 'This inventory code is already in use.']),

            Select::make('document_type')
                ->options([
                    'land_title' => 'Land title',
                    'deed'       => 'Deed',
                    'contract'   => 'Contract',
                    'other'      => 'Other',
                ])
                ->required(),

            Select::make('properties')
                ->relationship('properties', 'name')
                ->multiple()->searchable()->preload()
                ->requiredWithout('clients'),

            Select::make('clients')
                ->relationship('clients', 'name')
                ->multiple()->searchable()->preload()
                ->requiredWithout('properties'),

            FileUpload::make('file_location')
                ->label('File')
                ->disk('local')
                ->directory('documents')
                ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                ->maxSize(5120)
                ->required(),

            Textarea::make('description')
                ->columnSpanFull(),
        ]);
    }
}