<?php

namespace App\Filament\Resources\Technicians;

use App\Filament\Resources\Technicians\Pages\CreateTechnician;
use App\Filament\Resources\Technicians\Pages\EditTechnician;
use App\Filament\Resources\Technicians\Pages\ListTechnicians;
use App\Filament\Resources\Technicians\Pages\ViewTechnician;
use App\Filament\Resources\Technicians\Schemas\TechnicianForm;
use App\Filament\Resources\Technicians\Tables\TechniciansTable;
use App\Models\Technician;
use BackedEnum;
use UnitEnum;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TechnicianResource extends Resource
{
    protected static ?string $model = Technician::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $recordTitleAttribute = 'full_name';

    protected static UnitEnum|string|null $navigationGroup = 'Opérations';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return TechnicianForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TechniciansTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            InfolistSection::make('Profil')
                ->schema([
                    TextEntry::make('full_name')->label('Technicien'),
                    TextEntry::make('company.name')->label('Entreprise'),
                    TextEntry::make('email'),
                    TextEntry::make('phone'),
                    TextEntry::make('hired_at')->label('Date d\'embauche')->date(),
                ])
                ->columns(2),
            InfolistSection::make('Performance')
                ->schema([
                    TextEntry::make('interventions_count')
                        ->label('Total interventions')
                        ->state(fn (Technician $record) => $record->interventions_count ?? $record->interventions()->count()),
                    TextEntry::make('average_rating')
                        ->label('Note moyenne')
                        ->state(fn (Technician $record) => $record->average_rating ? number_format($record->average_rating, 2) : 'N/A'),
                    TextEntry::make('punctuality_rate')
                        ->label('Ponctualité')
                        ->state(fn (Technician $record) => number_format($record->punctuality_rate * 100, 1) . '%'),
                ])
                ->columns(3),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\InterventionsRelationManager::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withKpis();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTechnicians::route('/'),
            'create' => CreateTechnician::route('/create'),
            'edit' => EditTechnician::route('/{record}/edit'),
            'view' => ViewTechnician::route('/{record}'),
        ];
    }
}
