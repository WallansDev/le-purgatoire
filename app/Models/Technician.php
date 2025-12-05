<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Technician extends Model
{
    /** @use HasFactory<\Database\Factories\TechnicianFactory> */
    use HasFactory;

    protected $fillable = [
        'company_id',
        'first_name',
        'last_name',
        'phone',
        'email',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'full_name',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function interventions(): HasMany
    {
        return $this->hasMany(Intervention::class);
    }

    public function scopeWithKpis(Builder $query): Builder
    {
        return $query->withAvg(
            ['interventions as average_rating' => fn ($q) => $q->whereNotNull('service_note')],
            'service_note'
        )
            ->withCount([
                'interventions',
                'interventions as punctual_interventions_count' => fn ($q) => $q->where('was_late', false),
            ]);
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->last_name} {$this->first_name}");
    }

    public function getPunctualityRateAttribute(): float
    {
        $total = $this->interventions_count ?? $this->interventions()->count();

        if ($total === 0) {
            return 1.0;
        }

        $onTime = $this->punctual_interventions_count ?? $this->interventions()
            ->where('was_late', false)
            ->count();

        return round($onTime / $total, 4);
    }
}
