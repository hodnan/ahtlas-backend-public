<?php

namespace App\Services\Modules\Management\ControlCenter;

interface ControlCenterInterface
{
    const QUEUE = 'ccp-mail';
    const QUEUE_STAGE = 'ccp-stage';

    const NOTIFY_NONE = 0;
    const NOTIFY_CDC = 1;
    const NOTIFY_MANAGERS = 2;
    const NOTIFY_DIRECTOR = 3;
    const NOTIFY_CEO = 4;
    const STAGE_INTERVAL = 4;

    const NOTIFICATIONS = [
        self::NOTIFY_NONE => ['id' => self::NOTIFY_NONE, 'label' => 'Nenhuma Notifiaçação', 'color' => ''],
        self::NOTIFY_CDC => ['id' => self::NOTIFY_CDC, 'label' => 'Equipe CDC', 'color' => ''],
        self::NOTIFY_MANAGERS => ['id' => self::NOTIFY_MANAGERS, 'label' => 'Gestores', 'color' => ''],
        self::NOTIFY_DIRECTOR => ['id' => self::NOTIFY_DIRECTOR, 'label' => 'Diretor', 'color' => ''],
        self::NOTIFY_CEO => ['id' => self::NOTIFY_CEO, 'label' => 'CEO', 'color' => ''],
    ];

    const STAGE_NULL = ['id' => 0, 'border' => 'rgba(100, 181, 246)', 'rgb' => 'rgba(100, 181, 246, 0.3)',  'color' => 'blue-lighten-2'];

    const STAGE = [
        0 => ['id' => 0, 'border' => 'rgb(0, 200, 83)', 'rgb' => 'rgba(0, 200, 83, 0.3)',  'color' => 'green-accent-4'],
        1 => ['id' => 1, 'border' => 'rgb(212, 225, 87)', 'rgb' => 'rgba(212, 225, 87, 0.3)',  'color' => 'lime-lighten-1'],
        2 => ['id' => 2, 'border' => 'rgb(249, 168, 37)', 'rgb' => 'rgba(249, 168, 37,0.3)',  'color' => 'yellow-darken-3'],
        3 => ['id' => 3, 'border' => 'rgb(239, 108, 0)', 'rgb' => 'rgba(239, 108, 0, 0.3)',  'color' => 'orange-darken-3'],
        4 => ['id' => 4, 'border' => 'rgb(255, 61, 0)',  'rgb' => 'rgba(255, 61, 0, 0.3)',  'color' => 'deep-orange-accent-3']
    ];

    const STAGE_STATUS_STANDBY = 0;
    const STAGE_STATUS_VALIDATED = 1;
    const STAGE_STATUS_CANCELED = 2;
    const STAGE_STATUS_PROCCESS = 3;
    const STAGE_STATUS_FAILED = 4;

    const STAGE_STATUSES = [
        self::STAGE_STATUS_STANDBY =>  ['id' => self::STAGE_STATUS_STANDBY, 'label' => 'Standby', 'color' => 'purple-lighten-4'],
        self::STAGE_STATUS_VALIDATED =>  ['id' => self::STAGE_STATUS_VALIDATED, 'label' => 'Validado', 'color' => 'teal'],
        self::STAGE_STATUS_CANCELED =>  ['id' => self::STAGE_STATUS_CANCELED, 'label' => 'Cancelado', 'color' => 'deep-orange-accent-4'],
        self::STAGE_STATUS_PROCCESS =>  ['id' => self::STAGE_STATUS_PROCCESS, 'label' => 'Processando', 'color' => 'amber', 'disabled' => true],
        self::STAGE_STATUS_FAILED =>  ['id' => self::STAGE_STATUS_FAILED, 'label' => 'Erro', 'color' => 'orange-darken-4', 'disabled' => true],
    ];

    // Equipe CDC em cópia: config/ahtlas.php (ahtlas.control_center.cdc_users)
}
