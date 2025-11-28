<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Storage;

class Company extends Model
{
    /** @use HasFactory<\Database\Factories\CompanyFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'siret',
        'logo_path',
        'address_line1',
        'address_line2',
        'postal_code',
        'city',
        'country',
        'contact_name',
        'contact_email',
        'contact_phone',
    ];

    public function technicians(): HasMany
    {
        return $this->hasMany(Technician::class);
    }

    public function interventions(): HasManyThrough
    {
        return $this->hasManyThrough(Intervention::class, Technician::class);
    }

    /**
     * Get the URL of the company logo.
     */
    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->logo_path) {
            return null;
        }

        $path = $this->logo_path;
        
        // Remove any leading slashes
        $path = ltrim($path, '/');
        
        // If the path doesn't contain 'logos/', add it
        // Filament might store just the filename or the full path
        if (strpos($path, 'logos/') === false) {
            $path = 'logos/' . $path;
        }

        // Check if file exists in storage
        if (!Storage::disk('public')->exists($path)) {
            return null;
        }

        // Use asset() which is more reliable than Storage::url()
        return asset('storage/' . $path);
    }
}
