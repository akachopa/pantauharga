<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'organization_id',
        'name',
        'email',
        'password',
        'phone',
        'nip',
        'is_active',
        'is_internal',
        'is_demo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_internal' => 'boolean',
        'is_demo' => 'boolean',
        'password' => 'hashed',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id');
    }

    public function pointAssignments()
    {
        return $this->hasMany(PointAssignment::class, 'user_id');
    }

    public function primaryRole()
    {
        return $this->roles()->first()?->code ?? 'guest';
    }

    public function roleName()
    {
        return $this->roles()->first()?->name ?? 'Pengguna';
    }

    public function hasRole(string $code): bool
    {
        return $this->roles()->where('code', $code)->exists();
    }
}
