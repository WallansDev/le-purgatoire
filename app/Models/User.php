<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

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
}
