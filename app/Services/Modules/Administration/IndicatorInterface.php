<?php

namespace App\Services\Modules\Administration;

interface IndicatorInterface
{

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    const STATUSES = [
        self::STATUS_INACTIVE => ['id' => self::STATUS_INACTIVE, 'name' => 'Desabilitado', 'label' => self::STATUS_INACTIVE . ' - Desabilitado'],
        self::STATUS_ACTIVE => ['id' => self::STATUS_ACTIVE, 'name' => 'Ativo', 'label' => self::STATUS_ACTIVE . ' - Ativo'],
    ];

    const CALC_DIVISION = 0;
    const CALC_SUM = 1;

    const CALC_AVG = 0;
    const CALC_TOTAL = 1;

    const CALCS = [
        self::CALC_DIVISION => ['id' => self::CALC_DIVISION, 'name' => 'Divisão', 'caclRV' => [
            ['id' => self::CALC_TOTAL, 'label' => 'Total']
        ]],
        self::CALC_SUM => ['id' => self::CALC_SUM, 'name' => 'Soma', 'caclRV' => [
            ['id' => self::CALC_TOTAL, 'label' => 'Total'],
            ['id' => self::CALC_AVG, 'label' => 'Média'],
        ]],
    ];

    const DIRECION_POSITIVE = 0;
    const DIRECION_NEGATIVE = 1;

    const DIRECIONS = [
        self::DIRECION_POSITIVE =>  ['id' => self::DIRECION_POSITIVE, 'name' => 'Menor melhor', 'action' => 'Diminuir', 'icon' => 'mdi-arrow-down'],
        self::DIRECION_NEGATIVE =>  ['id' => self::DIRECION_NEGATIVE, 'name' => 'Maior melhor', 'action' => 'Aumentar', 'icon' => 'mdi-arrow-up'],
    ];

    const SYMBOL_NOT_APPLICABLE= 0;
    const SYMBOL_PERCENT = 1;
    const SYMBOL_REAL = 2;

    const SYMBOLS = [
        self::SYMBOL_NOT_APPLICABLE =>  ['id' => self::SYMBOL_NOT_APPLICABLE , 'name' => 'Ñ se aplica',  'position' => null],
        self::SYMBOL_PERCENT => ['id' => self::SYMBOL_PERCENT, 'name' => '%',  'position' => 1],
        self::SYMBOL_REAL =>  ['id' => self::SYMBOL_REAL, 'name' => 'R$',  'position' => 0],
    ];
}
