<?php

namespace App\Filament\Resources\Technicians\Tables;

use App\Models\Technician;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class TechniciansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('last_name')
            ->columns([
                TextColumn::make('full_name')
                    ->label('Technicien')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),
                TextColumn::make('company.name')
                    ->label('Entreprise')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('phone')
                    ->toggleable(),
                TextColumn::make('average_rating')
                    ->label('Note moy.')
                    ->state(fn (Technician $record) => $record->average_rating ? number_format($record->average_rating, 2) : 'N/A')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('punctuality_rate')
                    ->label('Ponctualité')
                    ->state(fn (Technician $record) => number_format($record->punctuality_rate * 100, 1) . '%')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('interventions_count')
                    ->label('Interventions')
                    ->counts('interventions')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('company_id')
                    ->label('Entreprise')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload(),
                TernaryFilter::make('is_active')
                    ->label('Actif')
                    ->boolean(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
