<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'email',
        'phone',
        'address_line1',
        'address_line2',
        'postal_code',
        'city',
        'country',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($organization) {
            if (empty($organization->slug)) {
                $organization->slug = Str::slug($organization->name);
            }
        });
    }

    /**
     * Get the groups for this organization.
     */
    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }

    /**
     * Get all users that belong to this organization through groups.
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function users()
    {
        return User::whereHas('groups', function ($query) {
            $query->where('organization_id', $this->id);
        });
    }

    /**
     * Get the default group for this organization.
     */
    public function defaultGroup()
    {
        return $this->groups()->where('is_default', true)->first();
    }

    /**
     * Get the companies that belong to this organization.
     */
    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'company_organization')
            ->withTimestamps();
    }
}

