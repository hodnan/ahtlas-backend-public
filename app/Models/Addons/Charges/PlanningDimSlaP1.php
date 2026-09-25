<?php

namespace App\Models\Addons\Charges;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class PlanningDimSlaP1 extends Model
{
    use HasFactory;

    protected $connection = 'mis_primary';
    protected $table = 'DB_MIS.dbo.TB_TEL_DIME_SLA_P1_AHTLAS_TMP';

    protected $fillable = [
        'CARGA_ID',
        'CD_DATA',
        'CD_HORA',
        'NO_SITE',
        'CD_SETOR',
        'PREV_REC',
        'PREV_ATE',
        'PREV_TMA',
        'PREV_NS',
        'PREV_HC',
        'PREV_HC_PAUSA',
        'PREV_DN',
        'PESO_DIA',
    ];
}
