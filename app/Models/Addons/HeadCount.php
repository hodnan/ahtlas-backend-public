<?php

namespace App\Models\Addons;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeadCount extends Model
{
    use HasFactory;
    protected $connection = 'mis_primary';
    protected $table = 'DB_RH.dbo.TB_RH_HEAD_COUNT_FT';
    public $timestamps = false;

    protected $fillable = [
        'CD_DATA',
        'CD_SETOR',
        'CD_MAT_DIRETOR',
        'CD_MAT_GERENTE_RELAC',
        'CD_MAT_GERENTE_LOCAL',
        'NU_HC_DIM',
        'NU_FERIAS_DIM',
        'NU_TREINAMENTO_DIM',
        'NU_TREINAMENTO_INICIAL_DIM',
        'NU_TREINAMENTO_MIGRACAO_DIM',
        'NU_TOTAL_DIM',
        'NU_HC_REAL',
        'NU_FERIAS_REAL',
        'NU_TREINAMENTO_REAL',
        'NU_TREINAMENTO_INICIAL_REAL',
        'NU_TREINAMENTO_MIGRACAO_REAL',
        'NU_TREINAMENTO_RECICLAGEM_REAL',
        'NU_TREINAMENTO_RETORNO_REAL',
        'NU_AFASTADO_REAL',
        'NU_TO_TOTAL_REAL',
        'NU_TO_ATIVO_REAL',
        'NU_TO_OPERACAO_REAL',
        'NU_TO_TREINAMENTO_REAL',
        'NU_TOTAL_REAL',
        'NU_TOTAL_ATIVO_REAL',
        'NU_HD_CLIENT_DIM',
        'NU_HC_DIF',
        'NU_TOTAL_DIF',
        'NU_FERIAS_DIF',
        'NU_TREINAMENTO_DIF',
        'NU_TREINAMENTO_PREV_NESTE_MES',
        'NU_TREINAMENTO_PREV_PROXIMO_MES',
        'NU_VERSAO',
    ];   
   
}
