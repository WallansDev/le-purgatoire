<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'phone',
        'email_verified_at',
        'password',
        'is_admin',
        'must_change_password',
    ];

    protected $appends = [
        'full_name',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'must_change_password' => 'boolean',
        ];
    }

    public function getFullNameAttribute(): string
    {
        $fullName = trim("{$this->first_name} {$this->last_name}");

        return $fullName !== '' ? $fullName : $this->name;
    }

    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    /**
     * Vérifie si cet utilisateur est le compte owner (premier utilisateur créé).
     */
    public function isOwner(): bool
    {
        // Le owner est l'utilisateur avec l'ID le plus petit (premier créé)
        // Utilisation d'un cache pour éviter les requêtes répétées
        static $firstUserId = null;

        if ($firstUserId === null) {
            $firstUserId = static::min('id');
        }

        return $firstUserId !== null && $this->id === $firstUserId;
    }

    /**
     * Get the groups that this user belongs to.
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_user')
            ->withTimestamps();
    }

    /**
     * Get all organizations that this user belongs to through groups.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function organizations()
    {
        return Organization::whereHas('groups.users', function ($query) {
            $query->where('users.id', $this->id);
        });
    }

    /**
     * Check if user has a specific permission for a resource.
     * This checks if any of the user's groups has the permission.
     */
    public function hasPermission(string $resource, string $action, ?string $scope = null): bool
    {
        if ($this->isOwner()) {
            return true;
        }

        return $this->groups()
            ->whereHas('permissions', function ($query) use ($resource, $action, $scope) {
                $query->where('resource', $resource)
                      ->where('action', $action);

                if ($scope !== null) {
                    $query->wherePivot('scope', $scope);
                }
            })
            ->exists();
    }

    /**
     * Check if user can perform an action on a specific resource within an organization.
     * This checks if the user has the permission in any group of that organization.
     */
    public function hasPermissionInOrganization(string $resource, string $action, Organization $organization, ?string $scope = null): bool
    {
        if ($this->isOwner()) {
            return true;
        }

        return $this->groups()
            ->where('organization_id', $organization->id)
            ->whereHas('permissions', function ($query) use ($resource, $action, $scope) {
                $query->where('resource', $resource)
                      ->where('action', $action);

                if ($scope !== null) {
                    $query->wherePivot('scope', $scope);
                }
            })
            ->exists();
    }

    /**
     * Get all organizations where user has a specific permission.
     */
    public function getOrganizationsWithPermission(string $resource, string $action, ?string $scope = null)
    {
        if ($this->isOwner()) {
            return Organization::all();
        }

        return Organization::whereHas('groups.users', function ($query) use ($resource, $action, $scope) {
            $query->where('users.id', $this->id)
                  ->whereHas('permissions', function ($permQuery) use ($resource, $action, $scope) {
                      $permQuery->where('resource', $resource)
                               ->where('action', $action);

                      if ($scope !== null) {
                          $permQuery->wherePivot('scope', $scope);
                      }
                  });
        })->get();
    }

    /**
     * Get organization IDs where user has a specific permission.
     */
    public function getOrganizationIdsWithPermission(string $resource, string $action, ?string $scope = null): array
    {
        if ($this->isOwner()) {
            return Organization::pluck('id')->toArray();
        }

        return Organization::whereHas('groups.users', function ($query) use ($resource, $action, $scope) {
            $query->where('users.id', $this->id)
                  ->whereHas('permissions', function ($permQuery) use ($resource, $action, $scope) {
                      $permQuery->where('resource', $resource)
                               ->where('action', $action);

                      if ($scope !== null) {
                          $permQuery->wherePivot('scope', $scope);
                      }
                  });
        })->pluck('id')->toArray();
    }

    // Convenience methods for backward compatibility and ease of use

    public function canReadCompanies(): bool { return $this->hasPermission('companies', 'read'); }
    public function canWriteCompanies(): bool { return $this->hasPermission('companies', 'write'); }
    public function canDeleteCompanies(): bool { return $this->hasPermission('companies', 'delete'); }

    public function canReadTechnicians(): bool { return $this->hasPermission('technicians', 'read'); }
    public function canWriteTechnicians(): bool { return $this->hasPermission('technicians', 'write'); }
    public function canDeleteTechnicians(): bool { return $this->hasPermission('technicians', 'delete'); }

    public function canReadInterventions(): bool { return $this->hasPermission('interventions', 'read'); }
    public function canWriteInterventions(): bool { return $this->hasPermission('interventions', 'write'); }
    public function canDeleteInterventions(): bool { return $this->hasPermission('interventions', 'delete'); }

    public function canReadOrganizations(): bool { return $this->hasPermission('organizations', 'read'); }
    public function canWriteOrganizations(): bool { return $this->hasPermission('organizations', 'write'); }
    public function canDeleteOrganizations(): bool { return $this->hasPermission('organizations', 'delete'); }

    public function canReadGroups(): bool { return $this->hasPermission('groups', 'read'); }
    public function canWriteGroups(): bool { return $this->hasPermission('groups', 'write'); }
    public function canDeleteGroups(): bool { return $this->hasPermission('groups', 'delete'); }

    public function canInvite(): bool { return $this->hasPermission('users', 'invite'); }

    // Legacy methods for backward compatibility
    public function canReadGroup(Group $group): bool { return $this->canReadGroups(); }
    public function canWriteGroup(Group $group): bool { return $this->canWriteGroups(); }
    public function canDeleteInGroup(Group $group): bool { return $this->canDeleteGroups(); }

    /**
     * Check if user belongs to a specific organization.
     */
    public function belongsToOrganization(Organization $organization): bool
    {
        return $this->groups()
            ->where('organization_id', $organization->id)
            ->exists();
    }

    /**
     * Get membership for a specific group.
     */
    public function getMembershipForGroup(Group $group)
    {
        return $this->groups()
            ->where('groups.id', $group->id)
            ->first();
    }

    /**
     * Get organizations where user has companies_read permission.
     */
    public function getOrganizationsWithCompaniesRead()
    {
        return $this->getOrganizationsWithPermission('companies', 'read');
    }

    /**
     * Get organization IDs where user has companies_read permission.
     */
    public function getOrganizationIdsWithCompaniesRead(): array
    {
        return $this->getOrganizationIdsWithPermission('companies', 'read');
    }

    /**
     * Get organization IDs where user has groups_read permission.
     */
    public function getOrganizationIdsWithGroupsRead(): array
    {
        return $this->getOrganizationIdsWithPermission('groups', 'read');
    }

    /**
     * Get organization IDs where user has groups_write permission.
     */
    public function getOrganizationIdsWithGroupsWrite(): array
    {
        return $this->getOrganizationIdsWithPermission('groups', 'write');
    }

    /**
     * Get organization IDs where user has groups_delete permission.
     */
    public function getOrganizationIdsWithGroupsDelete(): array
    {
        return $this->getOrganizationIdsWithPermission('groups', 'delete');
    }

    /**
     * Check if user can create users and invite to a specific organization.
     */
    public function canInviteToOrganization(Organization $organization): bool
    {
        return $this->hasPermissionInOrganization('users', 'invite', $organization);
    }

    /**
     * Get organizations where user can create users and invite members.
     */
    public function getOrganizationsWhereCanInvite()
    {
        return $this->getOrganizationsWithPermission('users', 'invite');
    }

    /**
     * Get groups where user can create users and invite members.
     */
    public function getGroupsWhereCanInvite()
    {
        if ($this->isOwner()) {
            return Group::with('organization')->get();
        }

        return Group::whereHas('users', function ($query) {
            $query->where('users.id', $this->id);
        })
        ->whereHas('permissions', function ($permQuery) {
            $permQuery->where('resource', 'users')
                     ->where('action', 'invite');
        })
        ->with('organization')
        ->get();
    }

    /**
     * Get all companies that the user has access to through their organizations.
     */
    public function companies()
    {
        if ($this->isOwner()) {
            return Company::all();
        }

        return Company::whereHas('organizations.groups.users', function ($query) {
            $query->where('users.id', $this->id);
        })->get();
    }
}

//     /**
//      * Check if user can read in a specific group (via group permissions).
//      * @deprecated Use canReadGroups() instead
//      */
//     public function canReadGroup(Group $group): bool
//     {
//         return $this->canReadGroups();
//     }

//     /**
//      * Check if user can write in a specific group (via group permissions).
//      * @deprecated Use canWriteGroups() instead
//      */
//     public function canWriteGroup(Group $group): bool
//     {
//         return $this->canWriteGroups();
//     }

//     /**
//      * Check if user can delete in a specific group (via group permissions).
//      * @deprecated Use canDeleteGroups() instead
//      */
//     public function canDeleteInGroup(Group $group): bool
//     {
//         return $this->canDeleteGroups();
//     }
    
//     /**
//      * Check if user can create users and invite to a specific organization.
//      * User must belong to a group with can_invite permission in that organization.
//      */
//     public function canInviteToOrganization(Organization $organization): bool
//     {
//         if ($this->isOwner()) {
//             return true;
//         }
        
//         return $this->groups()
//             ->whereHas('organization', function ($query) use ($organization) {
//                 $query->where('id', $organization->id);
//             })
//             ->where('groups.can_invite', true)
//             ->exists();
//     }
    
//     /**
//      * Get organizations where user can create users and invite members.
//      */
//     public function getOrganizationsWhereCanInvite()
//     {
//         if ($this->isOwner()) {
//             return Organization::all();
//         }
        
//         return Organization::whereHas('groups.users', function ($query) {
//             $query->where('users.id', $this->id)
//                   ->where('groups.can_invite', true);
//         })->get();
//     }
    
//     /**
//      * Get groups where user can create users and invite members.
//      */
//     public function getGroupsWhereCanInvite()
//     {
//         if ($this->isOwner()) {
//             return Group::with('organization')->get();
//         }
        
//         return Group::whereHas('users', function ($query) {
//             $query->where('users.id', $this->id);
//         })
//         ->where('can_invite', true)
//         ->with('organization')
//         ->get();
//     }

//     /**
//      * Check if user belongs to a specific organization.
//      */
//     public function belongsToOrganization(Organization $organization): bool
//     {
//         return $this->groups()
//             ->whereHas('organization', function ($query) use ($organization) {
//                 $query->where('id', $organization->id);
//             })
//             ->exists();
//     }

//     /**
//      * Get membership for a specific group.
//      */
//     public function getMembershipForGroup(Group $group)
//     {
//         return $this->groups()
//             ->where('groups.id', $group->id)
//             ->first();
//     }

//     /**
//      * Check if user can read Companies.
//      */
//     public function canReadCompanies(): bool
//     {
//         if ($this->isOwner()) {
//             return true;
//         }
        
//         return $this->groups()
//             ->where('groups.companies_read', true)
//             ->exists();
//     }

//     /**
//      * Check if user can write/modify Companies.
//      */
//     public function canWriteCompanies(): bool
//     {
//         if ($this->isOwner()) {
//             return true;
//         }
        
//         return $this->groups()
//             ->where('groups.companies_write', true)
//             ->exists();
//     }

//     /**
//      * Check if user can delete Companies.
//      */
//     public function canDeleteCompanies(): bool
//     {
//         if ($this->isOwner()) {
//             return true;
//         }
        
//         return $this->groups()
//             ->where('groups.companies_delete', true)
//             ->exists();
//     }

//     /**
//      * Check if user can read Technicians.
//      */
//     public function canReadTechnicians(): bool
//     {
//         if ($this->isOwner()) {
//             return true;
//         }
        
//         return $this->groups()
//             ->where('groups.technicians_read', true)
//             ->exists();
//     }

//     /**
//      * Check if user can write/modify Technicians.
//      */
//     public function canWriteTechnicians(): bool
//     {
//         if ($this->isOwner()) {
//             return true;
//         }
        
//         return $this->groups()
//             ->where('groups.technicians_write', true)
//             ->exists();
//     }

//     /**
//      * Check if user can delete Technicians.
//      */
//     public function canDeleteTechnicians(): bool
//     {
//         if ($this->isOwner()) {
//             return true;
//         }
        
//         return $this->groups()
//             ->where('groups.technicians_delete', true)
//             ->exists();
//     }

//     /**
//      * Check if user can read Interventions.
//      */
//     public function canReadInterventions(): bool
//     {
//         if ($this->isOwner()) {
//             return true;
//         }
        
//         return $this->groups()
//             ->where('groups.interventions_read', true)
//             ->exists();
//     }

//     /**
//      * Check if user can write/modify Interventions.
//      */
//     public function canWriteInterventions(): bool
//     {
//         if ($this->isOwner()) {
//             return true;
//         }
        
//         return $this->groups()
//             ->where('groups.interventions_write', true)
//             ->exists();
//     }

//     /**
//      * Check if user can delete Interventions.
//      */
//     public function canDeleteInterventions(): bool
//     {
//         if ($this->isOwner()) {
//             return true;
//         }
        
//         return $this->groups()
//             ->where('groups.interventions_delete', true)
//             ->exists();
//     }

//     /**
//      * Check if user can read Groups.
//      * User can read groups if they have groups_read permission in at least one group
//      * within any organization they belong to.
//      */
//     public function canReadGroups(): bool
//     {
//         if ($this->isOwner()) {
//             return true;
//         }

//         return $this->groups()
//             ->where('groups.groups_read', true)
//             ->exists();
//     }

//     /**
//      * Check if user can write/modify Groups.
//      * User can write groups if they have groups_write permission in at least one group
//      * within any organization they belong to.
//      */
//     public function canWriteGroups(): bool
//     {
//         if ($this->isOwner()) {
//             return true;
//         }

//         return $this->groups()
//             ->where('groups.groups_write', true)
//             ->exists();
//     }

//     /**
//      * Check if user can delete Groups.
//      * User can delete groups if they have groups_delete permission in at least one group
//      * within any organization they belong to.
//      */
//     public function canDeleteGroups(): bool
//     {
//         if ($this->isOwner()) {
//             return true;
//         }

//         return $this->groups()
//             ->where('groups.groups_delete', true)
//             ->exists();
//     }

//     /**
//      * Get organization IDs where user has groups_read permission.
//      */
//     public function getOrganizationIdsWithGroupsRead(): array
//     {
//         if ($this->isOwner()) {
//             return Organization::pluck('id')->toArray();
//         }

//         return Organization::whereHas('groups.users', function ($q) {
//             $q->where('users.id', $this->id)
//               ->where('groups.groups_read', true);
//         })->pluck('id')->toArray();
//     }

//     /**
//      * Get organization IDs where user has groups_write permission.
//      */
//     public function getOrganizationIdsWithGroupsWrite(): array
//     {
//         if ($this->isOwner()) {
//             return Organization::pluck('id')->toArray();
//         }

//         return Organization::whereHas('groups.users', function ($q) {
//             $q->where('users.id', $this->id)
//               ->where('groups.groups_write', true);
//         })->pluck('id')->toArray();
//     }

//     /**
//      * Get organization IDs where user has groups_delete permission.
//      */
//     public function getOrganizationIdsWithGroupsDelete(): array
//     {
//         if ($this->isOwner()) {
//             return Organization::pluck('id')->toArray();
//         }

//         return Organization::whereHas('groups.users', function ($q) {
//             $q->where('users.id', $this->id)
//               ->where('groups.groups_delete', true);
//         })->pluck('id')->toArray();
//     }

//     /**
//      * Check if user can read Organizations.
//      */
//     public function canReadOrganizations(): bool
//     {
//         if ($this->isOwner()) {
//             return true;
//         }
        
//         return $this->groups()
//             ->where('groups.organizations_read', true)
//             ->exists();
//     }

//     /**
//      * Check if user can write/modify Organizations.
//      */
//     public function canWriteOrganizations(): bool
//     {
//         if ($this->isOwner()) {
//             return true;
//         }
        
//         return $this->groups()
//             ->where('groups.organizations_write', true)
//             ->exists();
//     }

//     /**
//      * Check if user can delete Organizations.
//      */
//     public function canDeleteOrganizations(): bool
//     {
//         if ($this->isOwner()) {
//             return true;
//         }
        
//         return $this->groups()
//             ->where('groups.organizations_delete', true)
//             ->exists();
//     }

//     /**
//      * Get organizations where user has companies_read permission.
//      */
//     public function getOrganizationsWithCompaniesRead()
//     {
//         if ($this->isOwner()) {
//             return Organization::all();
//         }
        
//         return Organization::whereHas('groups.users', function ($q) {
//             $q->where('users.id', $this->id)
//               ->where('groups.companies_read', true);
//         })->get();
//     }

//     /**
//      * Get organization IDs where user has companies_read permission.
//      */
//     public function getOrganizationIdsWithCompaniesRead(): array
//     {
//         if ($this->isOwner()) {
//             return Organization::pluck('id')->toArray();
//         }

//         return Organization::whereHas('groups.users', function ($q) {
//             $q->where('users.id', $this->id)
//               ->where('groups.companies_read', true);
//         })->pluck('id')->toArray();
//     }

//     /**
//      * Get all companies that the user has access to through their organizations.
//      */
//     public function companies()
//     {
//         if ($this->isOwner()) {
//             return Company::all();
//         }

//         return Company::whereHas('organizations.groups.users', function ($query) {
//             $query->where('users.id', $this->id);
//         })->get();
//     }
// }
