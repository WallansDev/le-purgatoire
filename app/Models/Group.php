<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'name',
        'description',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * Get the organization that owns this group.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the users that belong to this group.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_user')
            ->withTimestamps();
    }

    /**
     * Get the permissions for this group.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'group_permissions')
            ->withPivot('scope')
            ->withTimestamps();
    }

    /**
     * Check if this group has a specific permission.
     */
    public function hasPermission(string $resource, string $action, ?string $scope = null): bool
    {
        $query = $this->permissions()
            ->where('resource', $resource)
            ->where('action', $action);

        if ($scope !== null) {
            $query->wherePivot('scope', $scope);
        }

        return $query->exists();
    }

    /**
     * Check if this group has permission to read a specific resource.
     */
    public function canRead(string $resource): bool
    {
        return $this->hasPermission($resource, 'read');
    }

    /**
     * Check if this group has permission to write a specific resource.
     */
    public function canWrite(string $resource): bool
    {
        return $this->hasPermission($resource, 'write');
    }

    /**
     * Check if this group has permission to delete a specific resource.
     */
    public function canDelete(string $resource): bool
    {
        return $this->hasPermission($resource, 'delete');
    }

    /**
     * Check if this group can invite users.
     */
    public function canInvite(): bool
    {
        return $this->hasPermission('users', 'invite');
    }

    /**
     * Accessor for can_read (checks if group has any read permission)
     */
    public function getCanReadAttribute(): bool
    {
        return $this->permissions()
            ->where('action', 'read')
            ->exists();
    }

    /**
     * Accessor for can_write (checks if group has any write permission)
     */
    public function getCanWriteAttribute(): bool
    {
        return $this->permissions()
            ->where('action', 'write')
            ->exists();
    }

    /**
     * Accessor for can_delete (checks if group has any delete permission)
     */
    public function getCanDeleteAttribute(): bool
    {
        return $this->permissions()
            ->where('action', 'delete')
            ->exists();
    }
}

