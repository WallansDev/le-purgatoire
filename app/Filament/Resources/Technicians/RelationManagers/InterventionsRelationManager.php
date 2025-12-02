<?php

namespace App\Filament\Resources\Technicians\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InterventionsRelationManager extends RelationManager
{
    protected static string $relationship = 'interventions';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('title')
                            ->label('Titre')
                            ->maxLength(255),
                        DateTimePicker::make('scheduled_at')
                            ->label('Prévue')
                            ->required()
                            ->seconds(false),
                        DateTimePicker::make('started_at')
                            ->label('Début réel')
                            ->seconds(false),
                        DateTimePicker::make('finished_at')
                            ->label('Fin réelle')
                            ->seconds(false),
                        Textarea::make('description')
                            ->label('Description')
                            ->columnSpanFull(),
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
                    ])
                    ->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('title')
                    ->label('Intervention')
                    ->searchable(),
                TextColumn::make('scheduled_at')
                    ->label('Prévue')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('delay_minutes')
                    ->label('Retard (min)')
                    ->sortable(),
                TextColumn::make('note')
                    ->label('Note')
                    ->formatStateUsing(fn ($state) => $state !== null ? $state . '/5' : '-')
                    ->sortable(),
                IconColumn::make('was_late')
                    ->label('Retard ?')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
