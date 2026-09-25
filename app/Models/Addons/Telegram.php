<?php

namespace App\Models\Addons;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Telegram extends Model
{
    use HasFactory;
    protected $connection = 'mis_primary';
    protected $table = 'DB_CORPORATIVO.dbo.TB_CORP_TELEGRAM';

    protected $fillable = [
            'ID_TELEGRAM',
            'CD_GRUPO',
            'NO_MENSAGEM',
            'FG_ENVIADO',
            'NU_ERRO',
            'DH_REGISTRO',
            'DH_ENVIO'
    ];

    public $timestamps = false;
    protected $primaryKey = 'ID_TELEGRAM';

  
  

}
