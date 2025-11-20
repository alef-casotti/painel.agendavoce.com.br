<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Verificar se o usuário é admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Verificar se o usuário é financeiro
     */
    public function isFinanceiro(): bool
    {
        return $this->role === 'financeiro';
    }

    /**
     * Verificar se o usuário é suporte
     */
    public function isSuporte(): bool
    {
        return $this->role === 'suporte';
    }

    /**
     * Verificar se o usuário é Customer Success Manager
     */
    public function isCustomerSuccessManager(): bool
    {
        return $this->role === 'customer_success_manager';
    }

    /**
     * Verificar se o usuário tem acesso a uma área específica
     */
    public function hasAccess(string $area): bool
    {
        if ($this->isAdmin()) {
            return true; // Admin tem acesso a tudo
        }

        return match($area) {
            'financeiro' => $this->isFinanceiro(),
            'suporte' => $this->isSuporte() || $this->isCustomerSuccessManager(),
            'customer_success' => $this->isCustomerSuccessManager(),
            default => false,
        };
    }
}
