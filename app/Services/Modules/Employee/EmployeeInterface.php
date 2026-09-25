<?php

namespace App\Services\Modules\Employee;

interface EmployeeInterface
{

    const STATUS_ACTIVE = 0;
    const STATUS_INACTIVE = 1;

    const STATUSES = [
        ['id' => self::STATUS_ACTIVE, 'name' => 'Ativo', 'label' => self::STATUS_ACTIVE . ' - Ativo'],
        ['id' => self::STATUS_INACTIVE, 'name' => 'Desligado', 'label' => self::STATUS_ACTIVE . ' - Desligado']
    ];


    const HIERARCHICAL_LEVEL = [
        1 => ['id' => 1, 'label' => 'Supervisor'],
        2 => ['id' => 2, 'label' => 'Coordenador'],
        3 => ['id' => 3, 'label' => 'Gerente'],
        4 => ['id' => 4, 'label' => 'Superintendente'],
        5 => ['id' => 5, 'label' => 'Diretor'],
        6 => ['id' => 6, 'label' => 'Diretor Geral'],
        0 => ['id' => 0, 'label' => 'Não se aplica'],
    ];
}
