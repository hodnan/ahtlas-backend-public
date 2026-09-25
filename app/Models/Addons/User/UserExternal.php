<?php

namespace App\Models\Addons\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;

class UserExternal extends Model
{
    use HasFactory, TimezoneTrait;

    protected $connection = 'mis_primary';
    protected $table = 'DB_RH.dbo.TB_QUADRO_DM_CLIENTES_EXTERNO_EMAIL';
    protected $primaryKey = "ID";

    protected $fillable = [
        'ID',
        'CD_MAT_ANALISTA',
        'NO_NOME',
        'NO_EMAIL',
        'NO_SUPERIOR',
        'EMAIL_SUPERVISOR',
        'CD_MAT_SUPER',
        'NO_CARGO',
        'CD_SETOR',
        'DH_REGISTR'
    ];
    // protected function casts(): array {return [];}
    // public $appends = [];
}
