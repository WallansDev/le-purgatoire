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
        // Permissions Companies
        'companies_read',
        'companies_write',
        'companies_delete',
        // Permissions Technicians
        'technicians_read',
        'technicians_write',
        'technicians_delete',
        // Permissions Interventions
        'interventions_read',
        'interventions_write',
        'interventions_delete',
        // Permissions Organizations
        'organizations_read',
        'organizations_write',
        'organizations_delete',
        // Permissions Groups
        'groups_read',
        'groups_write',
        'groups_delete',
        // Permission Invite
        'can_invite',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        // Permissions Companies
        'companies_read' => 'boolean',
        'companies_write' => 'boolean',
        'companies_delete' => 'boolean',
        // Permissions Technicians
        'technicians_read' => 'boolean',
        'technicians_write' => 'boolean',
        'technicians_delete' => 'boolean',
        // Permissions Interventions
        'interventions_read' => 'boolean',
        'interventions_write' => 'boolean',
        'interventions_delete' => 'boolean',
        // Permissions Organizations
        'organizations_read' => 'boolean',
        'organizations_write' => 'boolean',
        'organizations_delete' => 'boolean',
        // Permissions Groups
        'groups_read' => 'boolean',
        'groups_write' => 'boolean',
        'groups_delete' => 'boolean',
        // Permission Invite
        'can_invite' => 'boolean',
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
}

