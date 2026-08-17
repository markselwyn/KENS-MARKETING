<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
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
        'role', // <-- This is the magic key for your Admin/Staff security!
        'is_approved', // <-- Added this to handle the strict approval workflow!
        'revoked_at',
        'profile_photo_path',
        'preferences',
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
            'is_approved' => 'boolean',
            'revoked_at' => 'datetime',
            'last_seen' => 'datetime',
            'profile_photo_path' => 'encrypted',
            'preferences' => 'encrypted:array',
            'password' => 'hashed',
        ];
    }

    /**
     * Return application preferences merged with role-aware defaults.
     */
    public function appPreferences(): array
    {
        $defaults = [
            'theme' => 'system',
            'landing_page' => strtolower(trim($this->role)) === 'admin' ? 'dashboard' : 'inventory.index',
            'sidebar_state' => 'expanded',
            'reduced_motion' => false,
        ];

        return array_merge($defaults, $this->preferences ?? []);
    }
}
