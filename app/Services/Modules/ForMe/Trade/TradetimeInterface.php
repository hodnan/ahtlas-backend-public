<?php

namespace App\Services\Modules\ForMe\Trade;

interface TradetimeInterface
{
        // Status da requisição
        const STATUS_PENDING = 0;
        const STATUS_APPROVED = 1;
        const STATUS_CANCELED = 2;
        const STATUS_RENOVATED = 3;

        const STATUSLIST = [
            self::STATUS_PENDING => 'Pendente',
            self::STATUS_APPROVED => 'Concluído',
            self::STATUS_CANCELED => 'Cancelado',
            self::STATUS_RENOVATED => 'Renovado',
        ];

        const STATUSCOLOR = [
            self::STATUS_PENDING => ' bg-yellow-darken-1 ',
            self::STATUS_APPROVED => ' bg-green-darken-4 ',
            self::STATUS_CANCELED => ' bg-deep-orange-darken-4 ',
            self::STATUS_RENOVATED => ' bg-amber-darken-2 ',
        ];

        const STATUSLISTREG = [
            ['id' => self::STATUS_CANCELED, 'title' => 'Cancelar', 'disabled'=> false],
            ['id' => self::STATUS_RENOVATED, 'title' => 'Renovar', 'disabled'=> false],
            ['id' => self::STATUS_PENDING, 'title' => 'Pendenciar', 'disabled'=> true],
            ['id' => self::STATUS_APPROVED, 'title' => 'Concluir', 'disabled'=> true],
        ];

        const STATUSLISTFILTER = [
            ['id' => self::STATUS_PENDING, 'title' => 'Pendente'],
            ['id' => self::STATUS_CANCELED, 'title' => 'Cancelado'],
            ['id' => self::STATUS_RENOVATED, 'title' => 'Renovado'],
            ['id' => self::STATUS_APPROVED, 'title' => 'Concluído'],
         
        ];


       
}

