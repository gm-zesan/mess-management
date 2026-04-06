<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

/**
 * @method bool hasRole($roles) Check if user has a specific role
 * @method bool can($abilities, $arguments = []) Check if user has a specific permission
 * @method \Illuminate\Database\Eloquent\Collection getRoleNames() Get all roles assigned to user
 * @method \Illuminate\Database\Eloquent\Collection getPermissionNames() Get all permissions assigned to user
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'email_verified_at',
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

    /**
     * Get the meals for this user.
     */
    public function meals(): HasMany
    {
        return $this->hasMany(Meal::class);
    }
    

    public function messes(): BelongsToMany
    {
        return $this->belongsToMany(Mess::class, 'mess_user')
            ->withPivot(['status'])
            ->withTimestamps();
    }

    public function messUsers(): HasMany
    {
        return $this->hasMany(MessUser::class);
    }

    /**
     * Get the active mess for this user (first approved mess).
     */
    public function activeMess(): ?Mess
    {
        return $this->messes()
            ->where('status', 'approved')
            ->first();
    }

    /**
     * Get the deposits for this user.
     */
    public function deposits(): HasMany
    {
        return $this->hasMany(Deposit::class);
    }
}
