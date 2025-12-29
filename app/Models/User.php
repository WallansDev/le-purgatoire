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
     * Check if user can read in a specific group (via group permissions).
     */
    public function canReadGroup(Group $group): bool
    {
        if ($this->isOwner()) {
            return true;
        }
        
        return $this->groups()
            ->where('groups.id', $group->id)
            ->where('groups.can_read', true)
            ->exists();
    }

    /**
     * Check if user can write in a specific group (via group permissions).
     */
    public function canWriteGroup(Group $group): bool
    {
        if ($this->isOwner()) {
            return true;
        }
        
        return $this->groups()
            ->where('groups.id', $group->id)
            ->where('groups.can_write', true)
            ->exists();
    }

    /**
     * Check if user can delete in a specific group (via group permissions).
     */
    public function canDeleteInGroup(Group $group): bool
    {
        if ($this->isOwner()) {
            return true;
        }
        
        return $this->groups()
            ->where('groups.id', $group->id)
            ->where('groups.can_delete', true)
            ->exists();
    }
    
    /**
     * Check if user can create users and invite to a specific organization.
     * User must belong to a group with can_invite permission in that organization.
     */
    public function canInviteToOrganization(Organization $organization): bool
    {
        if ($this->isOwner()) {
            return true;
        }
        
        return $this->groups()
            ->whereHas('organization', function ($query) use ($organization) {
                $query->where('id', $organization->id);
            })
            ->where('groups.can_invite', true)
            ->exists();
    }
    
    /**
     * Get organizations where user can create users and invite members.
     */
    public function getOrganizationsWhereCanInvite()
    {
        if ($this->isOwner()) {
            return Organization::all();
        }
        
        return Organization::whereHas('groups.users', function ($query) {
            $query->where('users.id', $this->id)
                  ->where('groups.can_invite', true);
        })->get();
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
        ->where('can_invite', true)
        ->with('organization')
        ->get();
    }

    /**
     * Check if user belongs to a specific organization.
     */
    public function belongsToOrganization(Organization $organization): bool
    {
        return $this->groups()
            ->whereHas('organization', function ($query) use ($organization) {
                $query->where('id', $organization->id);
            })
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
}
