<?php

namespace App\Filament\Resources\Interventions\Pages;

use App\Filament\Resources\Interventions\InterventionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditIntervention extends EditRecord
{
    protected static string $resource = InterventionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Si la note est NULL, cocher la case "Non noté"
        if (is_null($data['note'] ?? null)) {
            $data['no_note'] = true;
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Si "Non noté" est coché, mettre note à NULL
        if (isset($data['no_note']) && $data['no_note']) {
            $data['note'] = null;
        }
        unset($data['no_note']); // Retirer le champ no_note qui n'existe pas en base

        return $data;
    }
}
