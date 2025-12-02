<?php

namespace App\Filament\Resources\Interventions\Pages;

use App\Filament\Resources\Interventions\InterventionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateIntervention extends CreateRecord
{
    protected static string $resource = InterventionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Si "Non noté" est coché, mettre note à NULL
        if (isset($data['no_note']) && $data['no_note']) {
            $data['note'] = null;
        }
        unset($data['no_note']); // Retirer le champ no_note qui n'existe pas en base

        return $data;
    }
}
