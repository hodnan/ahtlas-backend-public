<?php

namespace App\Services\Core\Notify;

interface NotifyInterface
{
    const QUEUE = "notify-distribuct";
    const STATUS_STANDBY = 0;
    const STATUS_DISTRIBUTING = 1;
    const STATUS_IN_PROGRESS = 2;
    const STATUS_FINISHED = 3;
    const STATUS_CANCELED = 4;

    const STATUSES = [
        self::STATUS_STANDBY =>  ['id' => self::STATUS_STANDBY, 'label' => 'Standby', 'color' => 'purple-lighten-4'],
        self::STATUS_DISTRIBUTING =>  ['id' => self::STATUS_DISTRIBUTING, 'label' => 'Destribuindo', 'color' => 'amber', 'disabled' => true],
        self::STATUS_IN_PROGRESS =>  ['id' => self::STATUS_IN_PROGRESS, 'label' => 'Em andamento', 'color' => 'teal'],
        self::STATUS_FINISHED =>  ['id' => self::STATUS_FINISHED, 'label' => 'Finalizado', 'color' => 'deep-orange-accent-4'],
        self::STATUS_CANCELED =>  ['id' => self::STATUS_CANCELED, 'label' => 'Cancelado', 'color' => 'orange-darken-4'],
    ];

    const CATEGORY_AHTLAS = 0;
    const CATEGORY_RV = 1;
    const CATEGORY_CDC = 2;
    const CATEGORY_COMMUNICATION = 3;
  
    const CATEGORIES = [
        self::CATEGORY_AHTLAS =>  ['id' => self::CATEGORY_AHTLAS, 'label' => 'Ahtlas', 'color' => 'purple-lighten-4', 'disabled' => true],
        self::CATEGORY_RV =>  ['id' => self::CATEGORY_RV, 'label' => 'RV', 'color' => 'teal', 'disabled' => true],
        self::CATEGORY_CDC =>  ['id' => self::CATEGORY_CDC, 'label' => 'Central de Controle', 'color' => 'deep-orange-accent-4'],
        self::CATEGORY_COMMUNICATION =>  ['id' => self::CATEGORY_COMMUNICATION, 'label' => 'Comunicação', 'color' => 'orange-darken-4'],
    ];

}
