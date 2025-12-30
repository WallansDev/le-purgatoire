<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Companies
            [
                'resource' => 'companies',
                'action' => 'read',
                'display_name' => 'Lire les entreprises',
                'description' => 'Permet de consulter la liste des entreprises',
            ],
            [
                'resource' => 'companies',
                'action' => 'write',
                'display_name' => 'Modifier les entreprises',
                'description' => 'Permet de créer et modifier les entreprises',
            ],
            [
                'resource' => 'companies',
                'action' => 'delete',
                'display_name' => 'Supprimer les entreprises',
                'description' => 'Permet de supprimer les entreprises',
            ],

            // Technicians
            [
                'resource' => 'technicians',
                'action' => 'read',
                'display_name' => 'Lire les techniciens',
                'description' => 'Permet de consulter la liste des techniciens',
            ],
            [
                'resource' => 'technicians',
                'action' => 'write',
                'display_name' => 'Modifier les techniciens',
                'description' => 'Permet de créer et modifier les techniciens',
            ],
            [
                'resource' => 'technicians',
                'action' => 'delete',
                'display_name' => 'Supprimer les techniciens',
                'description' => 'Permet de supprimer les techniciens',
            ],

            // Interventions
            [
                'resource' => 'interventions',
                'action' => 'read',
                'display_name' => 'Lire les interventions',
                'description' => 'Permet de consulter la liste des interventions',
            ],
            [
                'resource' => 'interventions',
                'action' => 'write',
                'display_name' => 'Modifier les interventions',
                'description' => 'Permet de créer et modifier les interventions',
            ],
            [
                'resource' => 'interventions',
                'action' => 'delete',
                'display_name' => 'Supprimer les interventions',
                'description' => 'Permet de supprimer les interventions',
            ],

            // Organizations
            [
                'resource' => 'organizations',
                'action' => 'read',
                'display_name' => 'Lire les organisations',
                'description' => 'Permet de consulter la liste des organisations',
            ],
            [
                'resource' => 'organizations',
                'action' => 'write',
                'display_name' => 'Modifier les organisations',
                'description' => 'Permet de créer et modifier les organisations',
            ],
            [
                'resource' => 'organizations',
                'action' => 'delete',
                'display_name' => 'Supprimer les organisations',
                'description' => 'Permet de supprimer les organisations',
            ],

            // Groups
            [
                'resource' => 'groups',
                'action' => 'read',
                'display_name' => 'Lire les groupes',
                'description' => 'Permet de consulter la liste des groupes',
            ],
            [
                'resource' => 'groups',
                'action' => 'write',
                'display_name' => 'Modifier les groupes',
                'description' => 'Permet de créer et modifier les groupes',
            ],
            [
                'resource' => 'groups',
                'action' => 'delete',
                'display_name' => 'Supprimer les groupes',
                'description' => 'Permet de supprimer les groupes',
            ],

            // Users
            [
                'resource' => 'users',
                'action' => 'invite',
                'display_name' => 'Inviter des utilisateurs',
                'description' => 'Permet d\'inviter de nouveaux utilisateurs',
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission['resource'] . '.' . $permission['action'],
                'display_name' => $permission['display_name'],
                'resource' => $permission['resource'],
                'action' => $permission['action'],
                'description' => $permission['description'],
            ]);
        }
    }
}
