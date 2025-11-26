<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'lastname',
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
     *
     */
    public function guardavida() {
        return $this->hasOne(Guardavida::class);
    }

    public function scopeHabilitados($query){
        return $query->where('enabled', true);
    }

    public function scopeDeshabilitados($query){
        return $query->where('enabled', false);
    }

    public static function obtenerPuesto($idUser, $idPuesto){
        $datosGuardavidas = User::select('*')
        ->where('user_id', $idUser)
        ->join('guardavidas', 'guardavidas.user_id', '=','users.id')
        ->join('puestos', 'guardavidas.puesto_id', '=', 'puestos.id')
        ->where('puestos.id', $idPuesto)
        ->first();

        return !empty($datosGuardavidas) ? $datosGuardavidas : null;

    }
}
