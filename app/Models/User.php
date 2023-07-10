<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enum\TipoDocumento;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
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
        "uuid",
        "nombre",
        "apellido",
        "documento",
        "tipo_documento",
        "cuit_cuil",
        "email",
        "password",
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ["password", "remember_token"];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        "tipo_documento" => TipoDocumento::class,
        "email_verified_at" => "datetime",
        "password" => "hashed",
    ];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    // Accessor/Mutator
    public function cuitCuil(): Attribute
    {
        return Attribute::make(
            // Lo transforma de la base de datos al Frontend
            get: fn ($value) => Str::substr($value, 0, 2) . '-' . Str::substr($value, 2, 8) . '-' . Str::substr($value, 10),
            // Lo transforma del Frontend a la base de datos
            set: fn ($value) => str_replace('-', '', $value)
        );
    }
}
