<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'resource',
        'action',
        'description',
    ];

    /**
     * Get the groups that have this permission.
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_permissions')
            ->withPivot('scope')
            ->withTimestamps();
    }

    /**
     * Check if this permission applies to a specific resource type.
     */
    public function isForResource(string $resource): bool
    {
        return $this->resource === $resource;
    }

    /**
     * Check if this permission allows a specific action.
     */
    public function allowsAction(string $action): bool
    {
        return $this->action === $action;
    }

    /**
     * Get the full permission key (resource.action).
     */
    public function getKey(): string
    {
        return $this->resource . '.' . $this->action;
    }

    /**
     * Scope to filter permissions by resource.
     */
    public function scopeForResource($query, string $resource)
    {
        return $query->where('resource', $resource);
    }

    /**
     * Scope to filter permissions by action.
     */
    public function scopeForAction($query, string $action)
    {
        return $query->where('action', $action);
    }
}
