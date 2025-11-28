<?php

namespace App\Filament\Resources\Technicians\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TechnicianForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Technicien')
                    ->schema([
                        TextInput::make('first_name')
                            ->label('Prénom')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('last_name')
                            ->label('Nom')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),
                        TextInput::make('phone')
                            ->label('Téléphone')
                            ->tel()
                            ->maxLength(30),
                    ])
                    ->columns(2),
                Section::make('Affectation')
                    ->schema([
                        Select::make('company_id')
                            ->label('Entreprise')
                            ->relationship('company', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        DatePicker::make('hired_at')
                            ->label('Date d\'embauche')
                            ->maxDate(now()),
                        Toggle::make('is_active')
                            ->label('Actif')
                            ->default(true),
                    ])
                    ->columns(3),
            ]);
    }
}
