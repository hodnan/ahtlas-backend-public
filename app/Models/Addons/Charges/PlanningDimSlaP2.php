<?php

namespace App\Models\Addons\Charges;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TimezoneTrait;

class PlanningDimSlaP2 extends Model
{
    use HasFactory, TimezoneTrait;

    protected $connection = 'mis_primary';
    protected $table = 'DB_MIS.dbo.TB_TEL_DIME_SLA_P2_AHTLAS_TMP';
   
    protected $fillable = [
        'ANO',
        'MES',
        'DATA',
        'CD_DPTO',
        'CD_TTV',
        'DIM',
        'DIM_FERIAS',
        'DIM_FTE',
        'PA',
        'DIM_PRO_RATA',
        'DIM_PRO_RATA_FERIAS',
        'VERSAO_CAPACITY',
        'PER_ESCALA',
        'AG_4HS',
        'AG_6HS',
        'AG_7HS',
        'HC_CLIENTE',
        'FTE_AJUSTADO',
        'TX_OCUP',
        'CARGA_ID',
    ];
   
}
