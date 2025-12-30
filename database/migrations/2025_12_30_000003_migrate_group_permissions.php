<?php

use App\Models\Group;
use App\Models\Permission;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $groups = Group::all();
        $permissions = Permission::all()->keyBy(function ($permission) {
            return $permission->resource . '.' . $permission->action;
        });

        foreach ($groups as $group) {
            $groupPermissions = [];

            // Migrer les permissions companies
            if ($group->companies_read ?? false) {
                $groupPermissions[] = ['permission_id' => $permissions['companies.read']->id, 'scope' => 'organization'];
            }
            if ($group->companies_write ?? false) {
                $groupPermissions[] = ['permission_id' => $permissions['companies.write']->id, 'scope' => 'organization'];
            }
            if ($group->companies_delete ?? false) {
                $groupPermissions[] = ['permission_id' => $permissions['companies.delete']->id, 'scope' => 'organization'];
            }

            // Migrer les permissions technicians
            if ($group->technicians_read ?? false) {
                $groupPermissions[] = ['permission_id' => $permissions['technicians.read']->id, 'scope' => 'organization'];
            }
            if ($group->technicians_write ?? false) {
                $groupPermissions[] = ['permission_id' => $permissions['technicians.write']->id, 'scope' => 'organization'];
            }
            if ($group->technicians_delete ?? false) {
                $groupPermissions[] = ['permission_id' => $permissions['technicians.delete']->id, 'scope' => 'organization'];
            }

            // Migrer les permissions interventions
            if ($group->interventions_read ?? false) {
                $groupPermissions[] = ['permission_id' => $permissions['interventions.read']->id, 'scope' => 'organization'];
            }
            if ($group->interventions_write ?? false) {
                $groupPermissions[] = ['permission_id' => $permissions['interventions.write']->id, 'scope' => 'organization'];
            }
            if ($group->interventions_delete ?? false) {
                $groupPermissions[] = ['permission_id' => $permissions['interventions.delete']->id, 'scope' => 'organization'];
            }

            // Migrer les permissions organizations
            if ($group->organizations_read ?? false) {
                $groupPermissions[] = ['permission_id' => $permissions['organizations.read']->id, 'scope' => 'organization'];
            }
            if ($group->organizations_write ?? false) {
                $groupPermissions[] = ['permission_id' => $permissions['organizations.write']->id, 'scope' => 'organization'];
            }
            if ($group->organizations_delete ?? false) {
                $groupPermissions[] = ['permission_id' => $permissions['organizations.delete']->id, 'scope' => 'organization'];
            }

            // Migrer les permissions groups
            if ($group->groups_read ?? false) {
                $groupPermissions[] = ['permission_id' => $permissions['groups.read']->id, 'scope' => 'organization'];
            }
            if ($group->groups_write ?? false) {
                $groupPermissions[] = ['permission_id' => $permissions['groups.write']->id, 'scope' => 'organization'];
            }
            if ($group->groups_delete ?? false) {
                $groupPermissions[] = ['permission_id' => $permissions['groups.delete']->id, 'scope' => 'organization'];
            }

            // Migrer la permission can_invite
            if ($group->can_invite ?? false) {
                $groupPermissions[] = ['permission_id' => $permissions['users.invite']->id, 'scope' => 'organization'];
            }

            // Attacher les permissions au groupe
            if (!empty($groupPermissions)) {
                $group->permissions()->attach(array_column($groupPermissions, 'permission_id'), ['scope' => 'organization']);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cette migration n'est pas réversible car les anciennes colonnes ont été supprimées
    }
};
