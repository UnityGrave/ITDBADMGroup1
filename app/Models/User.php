<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
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
        'email',
        'password',
        'preferred_currency',
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
     * Get the roles that belong to the user.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Check if the user has a specific role.
     */
    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    /**
     * Check if the user has any of the given roles.
     */
    public function hasAnyRole(array $roles): bool
    {
        return $this->roles()->whereIn('name', $roles)->exists();
    }

    /**
     * Assign a role to the user.
     */
    public function assignRole(string $roleName): void
    {
        $role = Role::where('name', $roleName)->first();
        if ($role && !$this->hasRole($roleName)) {
            $this->roles()->attach($role->id);
        }
    }

    /**
     * Remove a role from the user.
     */
    public function removeRole(string $roleName): void
    {
        $role = Role::where('name', $roleName)->first();
        if ($role) {
            $this->roles()->detach($role->id);
        }
    }

    /**
     * Get the orders for the user.
     */
    public function orders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the user's preferred currency.
     */
    public function preferredCurrency(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Currency::class, 'preferred_currency', 'code');
    }

    /**
     * Set the user's preferred currency.
     */
    public function setPreferredCurrency(string $currencyCode): void
    {
        $this->update(['preferred_currency' => strtoupper($currencyCode)]);
    }

    /**
     * Get the user's preferred currency code.
     * Returns null if no preference is set.
     */
    public function getPreferredCurrencyCode(): ?string
    {
        return $this->preferred_currency;
    }

    /**
     * Check if the user has a preferred currency set.
     */
    public function hasPreferredCurrency(): bool
    {
        return !empty($this->preferred_currency);
    }

    /**
     * Get the effective currency for this user.
     * Priority: user preference > session > default (USD).
     */
    public function getEffectiveCurrency(): string
    {
        // First check user preference
        if ($this->hasPreferredCurrency()) {
            return $this->preferred_currency;
        }

        // Then check session
        if (session()->has('currency')) {
            return session('currency');
        }

        // Finally, fall back to default
        return config('currency.base_currency', 'USD');
    }
}
