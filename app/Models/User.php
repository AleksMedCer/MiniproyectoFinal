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

    public const ROLE_ADMIN = 'admin';
    public const ROLE_GERENTE = 'gerente';
    public const ROLE_VENDEDOR = 'vendedor';
    public const ROLE_COMPRADOR = 'comprador';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
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

    public function hasRole(string|array $roles): bool
    {
        return in_array($this->role, (array) $roles, true);
    }

    // Relación si el usuario es vendedor
    public function productos()
    {
        return $this->hasMany(Producto::class, 'vendedor_id');
    }

    // Relación si el usuario es comprador
    public function compras()
    {
        return $this->hasMany(Venta::class, 'comprador_id');
    }

    // Relación para sus códigos 2FA
    public function codigosVerificacion()
    {
        return $this->hasMany(VerificacionCodigo::class);
    }
}
