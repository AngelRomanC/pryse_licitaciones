<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification; //notificaciones
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
//laravel permission
use Spatie\Permission\Traits\HasRoles;
use App\Traits\DateFormat;




class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    // use HasProfilePhoto;
    use Notifiable;
    // use TwoFactorAuthenticatable;
    use HasRoles;
    use Notifiable;
    use DateFormat;



    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'apellido_paterno',
        'apellido_materno',
        'numero',
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
    protected $appends = ['created_at_formatted'];

    //laravel permission

    public function scopeFiltro($query, array $filtros)
    {
        $query->when($filtros['profile'] ?? null, function ($query, $profile) {
            $query->join('model_has_roles as mhs', 'users.id', '=', 'mhs.model_id');
            $query->where('mhs.role_id', $profile);
        });

        $query->when($filtros['search'] ?? null, function ($query, $search) {
            $tokens = explode(' ', mb_strtoupper($search));
            foreach ($tokens as $token) {
                $query->where(function ($query) use ($token) {
                    $query->where(DB::raw('name'), 'like', '%' . trim($token) . '%')
                        ->orWhere('email', 'like', '%' . trim($token) . '%');
                });
            }
        });

        return $query;
    }
    public function isSuperAdmin(): bool
    {
        return false;
    }

    public function getAllowedViews($module): Collection
    {
        return $this->getAllPermissions()->where('module_key', $module)->pluck('arg');
    }

    public function getRolesArray(): Collection
    {
        return $this->roles()->get()->mapWithKeys(function ($role) {
            return [
                $role['name'] => [
                    'active' => true,
                    'description' => $role['description'], // Descripción desde la DB
                    'id' => $role['id'] // ID del rol
                ]
            ];
        });
    }

    public function getPermissionArray(): Collection
    {
        return $this->getAllPermissions()->mapWithKeys(function ($permission) {
            return [$permission['name'] => true];
        });
    }
    public function alumno()
    {
        return $this->hasOne(Alumno::class);
    }
    public function profesor()
    {
        return $this->hasOne(Profesor::class);
    }
    public function academico()
    {
        return $this->hasOne(Academico::class);
    }
    public function habito()
    {
        return $this->hasOne(Habito::class);
    }
    public function inteligencia()
    {
        return $this->hasOne(Inteligencia::class);
    }
    public function grupoAlumnos()
    {
        return $this->hasMany(Grupo_Alumnos::class);
    }

    /* public function notifications()
     {
         return $this->morphMany(DatabaseNotification::class, 'notifiable')->orderBy('created_at', 'desc');
     }*/
    public function departamento()
    {
        return $this->hasOne(UserDepartamento::class);
    }
    // Accesor para obtener directamente el departamento_id para catalago en front edit "UsuarioGeneral create.vue"
    public function getDepartamentoIdAttribute()
    {
        return $this->departamento->departamento_id ?? null;
    }
      public function getCreatedAtFormattedAttribute()
    {
        return $this->textFormatDate($this->created_at);
    }
}
