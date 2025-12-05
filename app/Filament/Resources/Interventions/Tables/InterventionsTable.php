<?php

namespace App\Filament\Resources\Interventions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InterventionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('scheduled_at', 'desc')
            ->columns([
                TextColumn::make('title')
                    ->label('Intervention')
                    ->searchable(),
                TextColumn::make('technician.full_name')
                    ->label('Technicien')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('technician.company.name')
                    ->label('Entreprise')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('scheduled_at')
                    ->label('Planifiée le')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('started_at')
                    ->label('Début réel')
                    ->dateTime()
                    ->toggleable(),
                TextColumn::make('delay_minutes')
                    ->label('Retard (min)')
                    ->sortable(),
                TextColumn::make('note')
                    ->label('Note')
                    ->formatStateUsing(fn ($state) => $state !== null ? $state . '/5' : '-')
                    ->sortable(),
                TextColumn::make('service_note')
                    ->label('Note service')
                    ->formatStateUsing(fn ($state) => $state !== null ? $state . '/5' : '-')
                    ->sortable(),
                IconColumn::make('was_late')
                    ->label('Retard ?')
                    ->boolean(),
            ])
            ->filters([
                Filter::make('scheduled_range')
                    ->label('Période planifiée')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from')->label('Du'),
                        \Filament\Forms\Components\DatePicker::make('until')->label('Au'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'] ?? null, fn (Builder $q, $date) => $q->whereDate('scheduled_at', '>=', $date))
                            ->when($data['until'] ?? null, fn (Builder $q, $date) => $q->whereDate('scheduled_at', '<=', $date));
                    }),
                SelectFilter::make('technician.company_id')
                    ->label('Entreprise')
                    ->relationship('technician.company', 'name')
                    ->preload()
                    ->searchable(),
                SelectFilter::make('technician_id')
                    ->label('Technicien')
                    ->relationship('technician', 'full_name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('note')
                    ->options([
                        5 => '5',
                        4 => '4',
                        3 => '3',
                        2 => '2',
                        1 => '1',
                        0 => '0',
                    ])
                    ->label('Note'),
                SelectFilter::make('service_note')
                    ->options([
                        5 => '5',
                        4 => '4',
                        3 => '3',
                        2 => '2',
                        1 => '1',
                        0 => '0',
                    ])
                    ->label('Note service'),
                TernaryFilter::make('was_late')->label('Retard'),
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
