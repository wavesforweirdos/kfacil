<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'avatar',
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
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function isAdmin(): bool
    {
        return $this->role?->name === 'admin';
    }

    public function isAgent(): bool
    {
        return $this->role?->name === 'agente';
    }

    public function panelPrefix(): string
    {
        return match ($this->role?->name) {
            'admin' => 'admin',
            'agente' => 'agente',
            default => '',
        };
    }

    public function initials(): string
    {
        return collect(explode(' ', $this->name))
            ->map(fn ($word) => strtoupper(substr($word, 0, 1)))
            ->take(2)
            ->implode('');
    }

    public function hasPermission($permission): bool
    {
        // Admin tiene todos los permisos
        if ($this->isAdmin()) {
            return true;
        }

        // Mapeo de permisos por rol
        $rolePermissions = [
            'agente' => [
                'view_pages',
                'create_pages',
                'edit_agent_pages',
                'edit_own_pages',
                'view_properties',
                'create_properties',
                'edit_own_properties',
            ],
        ];

        $permissions = $rolePermissions[$this->role?->name] ?? [];

        return in_array($permission, $permissions);
    }
}
