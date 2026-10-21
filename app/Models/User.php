<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Services\CatalogoRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'lastname',
        'dni',
        'email',
        'enabled',
        'password',
        'must_change_psw',
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
            'enabled' => 'boolean',
            'must_change_password' => 'boolean',
        ];
    }

    /**
     * Relación con Guardavidas
     */
    public function guardavida()
    {
        return $this->hasOne(Guardavida::class);
    }

    /** Inscripciones del usuario, una por temporada. */
    public function postulaciones()
    {
        return $this->hasMany(Postulacion::class);
    }

    /** Datos fijos que se precargan en cada inscripción. */
    public function perfil()
    {
        return $this->hasOne(Perfil::class);
    }

    /** Rol de mayor rango del usuario (para mostrar su etiqueta, color e ícono). */
    public function rolPrincipal(): ?Rol
    {
        return app(CatalogoRoles::class)->principal($this);
    }

    /** ¿Puede este usuario modificar a otro (datos, contraseña, rol)? Solo a quien no tenga mayor rango y nunca a un superadmin. */
    public function puedeGestionarA(User $otro): bool
    {
        return app(CatalogoRoles::class)->puedeGestionar($this, $otro);
    }

    public function scopeHabilitados($query)
    {
        return $query->where('enabled', true);
    }

    public static function obtenerPuesto($idUser, $idPuesto)
    {
        $datosGuardavidas = User::select('*')
            ->where('user_id', $idUser)
            ->join('guardavidas', 'guardavidas.user_id', '=', 'users.id')
            ->join('puestos', 'guardavidas.puesto_id', '=', 'puestos.id')
            ->where('puestos.id', $idPuesto)
            ->first();

        return ! empty($datosGuardavidas) ? $datosGuardavidas : null;

    }
}
