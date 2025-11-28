<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Intervention extends Model
{
    /** @use HasFactory<\Database\Factories\InterventionFactory> */
    use HasFactory;

    protected $fillable = [
        'technician_id',
        'scheduled_at',
        'started_at',
        'finished_at',
        'title',
        'description',
        'address',
        'note',
        'delay_minutes',
        'was_late',
        'is_completed',
        'non_completion_reason',
        'notes',
        'client_comments',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'note' => 'integer',
        'was_late' => 'boolean',
        'is_completed' => 'boolean',
    ];

    public function setTitleAttribute($value): void
    {
        $this->attributes['title'] = $value !== null ? mb_strtoupper($value) : null;
    }

    protected static function booted(): void
    {
        static::saving(function (Intervention $intervention): void {
            $intervention->syncDelayMetrics();
        });
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(Technician::class);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->whereNotNull('finished_at');
    }

    public function syncDelayMetrics(): void
    {
        if (! $this->scheduled_at instanceof CarbonInterface || ! $this->started_at instanceof CarbonInterface) {
            $this->delay_minutes = 0;
            $this->was_late = false;

            return;
        }

        $diff = $this->scheduled_at->diffInMinutes($this->started_at, false);

        if ($diff <= 0) {
            $this->delay_minutes = 0;
            $this->was_late = false;

            return;
        }

        $this->delay_minutes = $diff;
        $this->was_late = true;
    }
}
