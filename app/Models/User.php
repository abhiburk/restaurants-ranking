<?php

namespace App\Models;


use App\Enums\UserType;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

#[Guarded(['id'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, TwoFactorAuthenticatable, SoftDeletes, HasRoles, MustVerifyEmail;

    
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return match($panel->getId()) {
            'admin'   => $this->is_super_admin(),
            'partner' => $this->is_partner(),
            'contributor' => $this->is_contributor(),
            default   => false,
        };
    }

    public function restaurants(): HasMany
    {
        return $this->hasMany(Restaurant::class);
    }

    public function is_super_admin(): bool
    {
        return $this->role === UserType::SUPER_ADMIN->value;
    }

    public function is_partner(): bool
    {
        return $this->role === UserType::PARTNER->value;
    }

    public function is_user(): bool
    {
        return $this->role === UserType::USER->value;
    }

    public function is_contributor(): bool
    {
        return $this->role === UserType::CONTRIBUTOR->value;
    }

    public function contributors(): HasMany
    {
        return $this->hasMany(Contributor::class);
    }
}
