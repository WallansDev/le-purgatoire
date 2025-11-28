<?php

namespace App\Filament\Resources\Companies\Tables;

use App\Models\Company;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CompaniesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Entreprise')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->city),
                TextColumn::make('siret')
                    ->label('SIRET')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('contact_name')
                    ->label('Contact')
                    ->description(fn ($record) => $record->contact_email)
                    ->wrap(),
                TextColumn::make('technicians_count')
                    ->label('Techniciens')
                    ->counts('technicians')
                    ->sortable(),
                TextColumn::make('interventions_count')
                    ->label('Interventions')
                    ->counts('interventions')
                    ->sortable(),
                IconColumn::make('logo_path')
                    ->label('Logo')
                    ->boolean()
                    ->trueIcon('heroicon-m-check')
                    ->falseIcon('heroicon-m-x-mark')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('city')
                    ->label('Ville')
                    ->options(fn () => Company::query()
                        ->whereNotNull('city')
                        ->orderBy('city')
                        ->pluck('city', 'city')
                        ->toArray()),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
