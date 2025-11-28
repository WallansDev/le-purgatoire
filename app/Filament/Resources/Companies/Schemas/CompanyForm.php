<?php

namespace App\Filament\Resources\Companies\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CompanyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identité')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nom')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('siret')
                            ->label('SIRET')
                            ->required()
                            ->minLength(14)
                            ->maxLength(14)
                            ->unique(ignoreRecord: true),
                        FileUpload::make('logo_path')
                            ->label('Logo')
                            ->image()
                            ->disk('public')
                            ->directory('logos')
                            ->imageEditor()
                            ->imageCropAspectRatio('1:1')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Coordonnées')
                    ->schema([
                        TextInput::make('address_line1')
                            ->label('Adresse')
                            ->maxLength(255),
                        TextInput::make('address_line2')
                            ->label('Complément')
                            ->maxLength(255),
                        TextInput::make('postal_code')
                            ->label('Code postal')
                            ->maxLength(16),
                        TextInput::make('city')
                            ->label('Ville')
                            ->maxLength(255),
                        TextInput::make('country')
                            ->label('Pays')
                            ->default('France')
                            ->maxLength(255),
                    ])
                    ->columns(2),
                Section::make('Contact principal')
                    ->schema([
                        TextInput::make('contact_name')
                            ->label('Nom du contact')
                            ->maxLength(255),
                        TextInput::make('contact_email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),
                        TextInput::make('contact_phone')
                            ->label('Téléphone')
                            ->tel()
                            ->maxLength(30),
                    ])
                    ->columns(3),
            ]);
    }
}
