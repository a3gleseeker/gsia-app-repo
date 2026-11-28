<?php

namespace App\Filament\Resources\Properties\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PropertyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->numeric(),
                TextInput::make('name')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('link'),
                TextInput::make('type')
                    ->required(),
                TextInput::make('size')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->default(0.0)
                    ->prefix('$'),
                Select::make('status')
                    ->options([
                        'active'   => 'Active',
                        'sold'     => 'Sold',
                        'archived' => 'Archived',
                    ])
                    ->default('active')
                    ->required(),
                TextInput::make('postal')
                    ->required(),
                TextInput::make('city')
                    ->required(),
                TextInput::make('latitude')
                    ->numeric(),
                TextInput::make('longitude')
                    ->numeric(),
            ]);
    }
}
