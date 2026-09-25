<?php

namespace App\Services\Modules\Administration;

interface BonusInterface
{

    const STATUS_YEAR_ACTIVE = 1;
    const STATUS_YEAR_INACTIVE = 0;

    const YEAR_STATUSES = [
        self::STATUS_YEAR_INACTIVE => ['id' => self::STATUS_YEAR_INACTIVE, 'name' => 'Inativo', 'label' => self::STATUS_YEAR_INACTIVE . ' - Inativo'],
        self::STATUS_YEAR_ACTIVE => ['id' => self::STATUS_YEAR_ACTIVE, 'name' => 'Ativo', 'label' => self::STATUS_YEAR_ACTIVE . ' - Ativo'],
    ];

    const BLOCK_RANGES = [
        ['id' => 0, 'display' => false, 'readonly' => false, 'grade' => null,  'gr' => null, 'rgb' => '#BDBDBD', 'border' => 'brand_preto', 'color' => 'grey-lighten-1', 'icon' => 'mdi-power-plug-off-outline'],
        ['id' => 1, 'display' => false, 'readonly' => false, 'grade' =>    0,  'gr' =>    0, 'rgb' => '#DD2C00', 'border' => 'error', 'color' => 'deep-orange-accent-4', 'icon' => 'mdi-battery-minus-outline',],
        ['id' => 2, 'display' =>  true, 'readonly' => false, 'grade' =>    0,  'gr' =>   75, 'rgb' => '#FBC02D', 'border' => 'warning', 'color' => 'yellow-darken-2', 'icon' => 'mdi-battery-30',],
        ['id' => 3, 'display' =>  true, 'readonly' =>  true, 'grade' =>   10,  'gr' =>  100, 'rgb' => '#388E3C', 'border' => 'info', 'color' => 'green-darken-2', 'icon' => 'mdi-battery-70',],
        ['id' => 4, 'display' =>  true, 'readonly' => false, 'grade' =>   20,  'gr' =>  150, 'rgb' => '#0277BD', 'border' => 'primary', 'color' => 'light-blue-darken-3', 'icon' => 'mdi-battery-plus',],
    ];

    const HIERARCHICAL_LEVEL = [
        ['id' => 0, 'display' => false, 'name' => 'Operacional'         , 'message' => null],
        ['id' => 1, 'display' => false, 'name' => 'Supervisão'          , 'message' => null],
        ['id' => 2, 'display' => false, 'name' => 'Coordenação'         , 'message' => null],
        ['id' => 3, 'display' =>  true, 'name' => 'Gerência'            , 'message' => 'Dados serão conforme hierarquia'],
        ['id' => 4, 'display' =>  true, 'name' => 'Superintendência'    , 'message' => 'Dados serão conforme hierarquia'],
        ['id' => 5, 'display' =>  true, 'name' => 'Diretoria'           , 'message' => ''],
        ['id' => 6, 'display' =>  true, 'name' => 'CEO'                 , 'message' => 'Dados serão aplicados para todos os paineis'],
    ]; 

    const ACCUMULATION_TYPE_MANUAL = 0;
    const ACCUMULATION_TYPE_SUM = 1;
    const ACCUMULATION_TYPE_AVERAGE = 2;
    const ACCUMULATION_TYPE_EQUAL = 3;

    const ACCUMULATION_TYPES = [
        ['id' => 0, 'name' => 'Manual'],
        ['id' => 1, 'name' => 'Soma do mensal'],
        ['id' => 2, 'name' => 'Média do mensal'],
        ['id' => 3, 'name' => 'Igual do mensal'],
    ];

    const PANEL_STATUS_STANDBY = 0;
    const PANEL_STATUS_PENDING = 1;
    const PANEL_STATUS_APPROVED = 2;
    const PANEL_STATUS_REPROVED = 3;
    const PANEL_STATUS_CANCELED = 4;

    const PANEL_STATUSES = [
        self::PANEL_STATUS_STANDBY =>  ['id' => self::PANEL_STATUS_STANDBY, 'label' => 'Standby', 'color' => 'purple-lighten-4'],
        self::PANEL_STATUS_PENDING =>  ['id' => self::PANEL_STATUS_PENDING, 'label' => 'Pendente', 'color' => 'amber', 'disabled' => true],
        self::PANEL_STATUS_APPROVED =>  ['id' => self::PANEL_STATUS_APPROVED, 'label' => 'Aprovado', 'color' => 'teal'],
        self::PANEL_STATUS_REPROVED =>  ['id' => self::PANEL_STATUS_REPROVED, 'label' => 'Reprovado', 'color' => 'deep-orange-accent-4'],
        self::PANEL_STATUS_CANCELED =>  ['id' => self::PANEL_STATUS_CANCELED, 'label' => 'Cancelado', 'color' => 'orange-darken-4'],
    ];
}
