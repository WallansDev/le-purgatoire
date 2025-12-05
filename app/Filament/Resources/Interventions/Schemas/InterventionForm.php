<?php

namespace App\Filament\Resources\Interventions\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
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
                            ->placeholder('Non assigné'),
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
                        Checkbox::make('no_note')
                            ->label('Non noté')
                            ->dehydrated(false)
                            ->live()
                            ->afterStateUpdated(function (Set $set, $state) {
                                if ($state) {
                                    $set('note', null);
                                }
                            }),
                        Select::make('note')
                            ->label('Note')
                            ->options([
                                0 => '0',
                                1 => '1',
                                2 => '2',
                                3 => '3',
                                4 => '4',
                                5 => '5',
                            ])
                            ->native(false)
                            ->disabled(fn (Get $get): bool => $get('no_note') === true)
                            ->visible(fn (Get $get): bool => ! $get('no_note')),
                        Checkbox::make('no_service_note')
                            ->label('Non notée (service technique)')
                            ->dehydrated(false)
                            ->live()
                            ->afterStateUpdated(function (Set $set, $state) {
                                if ($state) {
                                    $set('service_note', null);
                                }
                            }),
                        Select::make('service_note')
                            ->label('Note service technique')
                            ->options([
                                0 => '0',
                                1 => '1',
                                2 => '2',
                                3 => '3',
                                4 => '4',
                                5 => '5',
                            ])
                            ->native(false)
                            ->disabled(fn (Get $get): bool => $get('no_service_note') === true)
                            ->visible(fn (Get $get): bool => ! $get('no_service_note')),
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
