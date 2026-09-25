<?php

namespace App\Models\Core;

use App\Models\Modules\Employee\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use SoftDeletes, HasApiTokens, HasFactory, Notifiable;

    protected $connection = 'core';
    protected $table = 'users';
    public $incrementing = false;

    protected $with = ['avatar', 'employee.sectorN1'];

    protected $fillable = [
        'id',
        'username',
        'name',
        'position',
        'position_summary',
        'uf',
        'email',
        'type',
        'hierarchical_level',
        'sector_n1_id',
        'sector_n2_id',
        'password',
        'birthdate',
        'status',
        'active',
        'staff',
        'deleted_at',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function avatar()
    {
        return $this->belongsTo(UserAvatar::class, 'username', 'username');
    }


    public function admin()
    {
        return $this->belongsTo(UserAdmin::class, 'username', 'username');
    }


    public function isAdmin()
    {
        // Verifica se existe um registro em user_admins para o username do usuário atual.
        $admin = $this->admin()->first();

        // Verifica se o admin existe e se a data de expiração não passou.
        if ($admin && (!$admin->expiration_at || $admin->expiration_at->isFuture())) {
            return true;
        }

        return false;
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'username', 'username');
    }
    
}
