<?php

namespace App\Services\Modules\Administration;

interface KpiSourceInterface
{
    const SERVER = 'MISCTRPPW05';
    const DB = 'DB_KPI';

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    const STATUSES = [
        self::STATUS_INACTIVE => ['id' => self::STATUS_INACTIVE, 'name' => 'Desabilitado', 'label' => self::STATUS_INACTIVE . ' - Desabilitado'],
        self::STATUS_ACTIVE => ['id' => self::STATUS_ACTIVE, 'name' => 'Ativo', 'label' => self::STATUS_ACTIVE . ' - Ativo'],
    ];

    
}
