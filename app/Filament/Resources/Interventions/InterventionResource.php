<?php

namespace App\Filament\Resources\Interventions;

use App\Filament\Resources\Interventions\Pages\CreateIntervention;
use App\Filament\Resources\Interventions\Pages\EditIntervention;
use App\Filament\Resources\Interventions\Pages\ListInterventions;
use App\Filament\Resources\Interventions\Schemas\InterventionForm;
use App\Filament\Resources\Interventions\Tables\InterventionsTable;
use App\Models\Intervention;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class InterventionResource extends Resource
{
    protected static ?string $model = Intervention::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $recordTitleAttribute = 'title';

    protected static UnitEnum|string|null $navigationGroup = 'Opérations';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return InterventionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InterventionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInterventions::route('/'),
            'create' => CreateIntervention::route('/create'),
            'edit' => EditIntervention::route('/{record}/edit'),
        ];
    }
}
