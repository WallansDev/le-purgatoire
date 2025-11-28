<?php

namespace App\Filament\Resources\Interventions\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class InterventionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Planification')
                    ->schema([
                        Select::make('technician_id')
                            ->label('Technicien')
                            ->relationship('technician', 'full_name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        DateTimePicker::make('scheduled_at')
                            ->label('Date prévue')
                            ->seconds(false)
                            ->required(),
                        DateTimePicker::make('started_at')
                            ->label('Début réel')
                            ->seconds(false),
                        DateTimePicker::make('finished_at')
                            ->label('Fin réelle')
                            ->seconds(false),
                    ])
                    ->columns(2),
                Section::make('Contenu')
                    ->schema([
                        TextInput::make('title')
                            ->label('Titre')
                            ->maxLength(255),
                        Textarea::make('description')
                            ->label('Description')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Qualité')
                    ->schema([
                        Select::make('note')
                            ->label('Note')
                            ->options([
                                1 => '1',
                                2 => '2',
                                3 => '3',
                                4 => '4',
                                5 => '5',
                            ])
                            ->native(false),
                        TextInput::make('delay_minutes')
                            ->label('Retard (min)')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false),
                        Toggle::make('was_late')
                            ->label('En retard')
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(3),
            ]);
    }
}
