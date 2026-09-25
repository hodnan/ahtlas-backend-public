<?php

namespace App\Services\Modules\Administration;

interface TermInterface
{
    const QUEUE = "rv-term_to_signature";

    const STORAGE_FILE_DISK = 'nas';
    const STORAGE_FILE_TEMP = 'temp';

    const STATUS_STANDBY = 0;
    const STATUS_PENDING = 1;
    const STATUS_APPROVED = 2;
    const STATUS_REPROVED = 3;
    const STATUS_CANCELED = 4;

    const STATUSES = [
        self::STATUS_STANDBY =>  ['id' => self::STATUS_STANDBY, 'label' => 'Standby', 'color' => 'purple-lighten-4'],
        self::STATUS_PENDING =>  ['id' => self::STATUS_PENDING, 'label' => 'Pendente', 'color' => 'amber', 'disabled' => true],
        self::STATUS_APPROVED =>  ['id' => self::STATUS_APPROVED, 'label' => 'Aprovado', 'color' => 'teal'],
        self::STATUS_REPROVED =>  ['id' => self::STATUS_REPROVED, 'label' => 'Reprovado', 'color' => 'deep-orange-accent-4'],
        self::STATUS_CANCELED =>  ['id' => self::STATUS_CANCELED, 'label' => 'Cancelado', 'color' => 'orange-darken-4'],
    ];

    const SIGNED_PENDING = null;
    const SIGNED_REJECTED = 0;
    const SIGNED_ACCEPTED = 1;

    const SIGNED = [
        self::SIGNED_PENDING =>  ['id' => self::SIGNED_PENDING, 'label' => 'Pendente', 'color' => 'amber', 'rgb' => '255, 193, 7'],
        self::SIGNED_REJECTED => ['id' => self::SIGNED_REJECTED, 'label' => 'Rejeitado', 'color' => 'deep-orange-accent-4', 'rgb' => '221, 44, 0'],
        self::SIGNED_ACCEPTED => ['id' => self::SIGNED_ACCEPTED, 'label' => 'Aceito', 'color' => 'teal', 'rgb' => '0, 150, 136'],
    ];

    const LEVELS = [
        0 => ['id' => 0, 'short' => 'NOV', 'label' => 'Novato',],
        1 => ['id' => 1, 'short' => 'VET', 'label' => 'Veterano',],
        2 => ['id' => 2, 'short' => 'AMB', 'label' => 'Ambos',],
    ];

    const POSITIONS = [
        0 => ['id' => 0, 'short' => 'AGE', 'label' => 'Agente'],
        1 => ['id' => 1, 'short' => 'SUP', 'label' => 'Supervisor'],
        2 => ['id' => 2, 'short' => 'COO', 'label' => 'Coordenador'],
    ];

    const DIRECTIVE_BASKET = 0;
    const DIRECTIVE_ACCELERATOR = 1;
    const DIRECTIVE_DEFLATOR = 2;
    const DIRECTIVE_ELIMINATION = 3;
    const DIRECTIVE_RESULT = 4;

    const TERM_DIRECTIVES = [
        self::DIRECTIVE_BASKET => 'Cesta',
        self::DIRECTIVE_BASKET => 'Acelerador',
        self::DIRECTIVE_BASKET => 'Deflator',
        self::DIRECTIVE_BASKET => 'Eliminatório',
        self::DIRECTIVE_RESULT => 'Resultado final',
    ];


    const CACHE_SUMMARIZED_TERMS = 'CACHE_SUMMARIZED_TERMS';

}
