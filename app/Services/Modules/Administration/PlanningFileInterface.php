<?php

namespace App\Services\Modules\Administration;

use App\Models\Modules\TacticalCenter\HeadCount\HeadCount;

interface PlanningFileInterface
{
    const QUEUE = "planning-charge";
    const STORAGE_FILE_PATH = 'Charge/Planning';
    const STORAGE_FILE_DISK = 'nas';

    const STATUS_STANDBY = 0;
    const STATUS_LOADING = 1;
    const STATUS_PENDING = 2;
    const STATUS_COMPLETED = 3;
    const STATUS_CANCELED = 4;
    const STATUS_ERROR = 5;

    const STATUSES = [
        self::STATUS_STANDBY => ['id' => self::STATUS_STANDBY, 'name' => 'Standby', 'color' => 'purple-lighten-4'],
        self::STATUS_LOADING => ['id' => self::STATUS_LOADING, 'name' => 'Loading', 'color' => 'lime-darken-2',],
        self::STATUS_PENDING => ['id' => self::STATUS_PENDING, 'name' => 'Pendente', 'color' => 'amber',],
        self::STATUS_COMPLETED => ['id' => self::STATUS_COMPLETED, 'name' => 'Concluído', 'color' => 'green-darken-1'],
        self::STATUS_CANCELED => ['id' => self::STATUS_CANCELED, 'name' => 'Cancelado', 'color' => 'orange-darken-4'],
        self::STATUS_ERROR => ['id' => self::STATUS_ERROR, 'name' => 'Falha', 'color' => 'deep-orange-accent-4'],
    ];

    const TYPE_DIM_SLA_P1 = 0;
    const TYPE_DIM_SLA_P2 = 1;
    const TYPE_DIM_HC = 2;

    const CHARGE_METHOD_DELETE = 1;
    const CHARGE_METHOD_UPDATE = 2;

    const TYPES = [
        self::TYPE_DIM_SLA_P1 =>  ['id' => self::TYPE_DIM_SLA_P1, 'name' => 'Sla Dim - P1', 'model' => 'SLA_DIM_P1.csv', 'charge' => 'chargeSLAP1', 'chargeMethod'=> self::CHARGE_METHOD_UPDATE],
        self::TYPE_DIM_SLA_P2 => ['id' => self::TYPE_DIM_SLA_P2, 'name' => 'Sla Dim - P2', 'model' => 'SLA_DIM_P2.csv', 'charge' => 'chargeSLAP2', 'chargeMethod'=> self::CHARGE_METHOD_UPDATE],
        self::TYPE_DIM_HC =>  ['id' => self::TYPE_DIM_HC, 'name' => 'HC Dim', 'model' => 'HC_DIM.csv', 'charge' => 'chargeHC', 'chargeMethod'=> self::CHARGE_METHOD_DELETE],
    ];

    const MODELS = [
        self::TYPE_DIM_SLA_P1 => ['model' => HeadCount::class, 'after_charge' => null],
        self::TYPE_DIM_SLA_P2 => ['model' => HeadCount::class, 'after_charge' => null],
        self::TYPE_DIM_HC =>  ['model' => HeadCount::class, 'after_charge' => 'chargeHCAfter'],
    ];

    const DEFAULT_FIELDS_DIM_SLA_P1 = ['CD_DATA', 'CD_HORA', 'NO_SITE', 'CD_SETOR', 'PREV_REC', 'PREV_ATE', 'PREV_TMA', 'PREV_NS', 'PREV_HC', 'PREV_HC_PAUSA', 'PREV_DN', 'PESO_DIA'];
    const DEFAULT_FIELDS_DIM_SLA_P2 = ['ANO', 'MES', 'DATA', 'CD_DPTO', 'CD_TTV', 'DIM', 'DIM_FERIAS', 'DIM_FTE', 'PA', 'DIM_PRO_RATA', 'DIM_PRO_RATA_FERIAS', 'VERSAO_CAPACITY', 'PER_ESCALA', 'AG_4HS', 'AG_6HS', 'AG_7HS', 'HC_CLIENTE', 'FTE_AJUSTADO', 'TX_OCUP'];
    const DEFAULT_FIELDS_DIM_HC = ['CD_SETOR', 'DT_DATA', 'NU_TREINO', 'NU_TR_INI', 'NU_TR_MIG', 'NU_HC', 'NU_FERIAS', 'NU_TOTAL', 'NU_CLI', 'CD_MAT_GERENTE_RELAC', 'CD_MAT_GERENTE_LOCAL', 'CD_MAT_DIRETOR', 'NU_VERSAO'];

    const DEFAULT_FIELDS = [
        self::TYPE_DIM_SLA_P1 =>  ['id' => self::TYPE_DIM_SLA_P1, 'fields' => self::DEFAULT_FIELDS_DIM_SLA_P1],
        self::TYPE_DIM_SLA_P2 => ['id' => self::TYPE_DIM_SLA_P2, 'fields' => self::DEFAULT_FIELDS_DIM_SLA_P2],
        self::TYPE_DIM_HC =>  ['id' => self::TYPE_DIM_HC, 'fields' => self::DEFAULT_FIELDS_DIM_HC],
    ];
}
