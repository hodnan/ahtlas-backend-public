<?php

namespace App\Services\Modules\TacticalCenter\Report;

interface ReportInterface
{

    const STORAGE_FILE_PATH = 'Reports';
    const STORAGE_FILE_DISK = 'nas';

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_ALL = 2;

    const STATUSES = [
        self::STATUS_INACTIVE => ['id' => self::STATUS_INACTIVE, 'name' => 'Descontinuado', 'label' => self::STATUS_ACTIVE . ' - Descontinuado', 'color' => 'deep-orange-darken-3'],
        self::STATUS_ACTIVE => ['id' => self::STATUS_ACTIVE, 'name' => 'Ativo', 'label' => self::STATUS_ACTIVE . ' - Ativo', 'color' => 'teal-darken-1'],
        self::STATUS_ALL => ['id' => null, 'name' => 'Todos', 'label' => 'Todos','color' => 'medium-emphasis'],
    ];

    const REPORT_TYPE_ALL = 0;
    const REPORT_TYPE_CUBE = 1;
    const REPORT_TYPE_DASH = 2;
    const REPORT_TYPE_EXCEL = 3;
    const REPORT_TYPE_MAILING = 4;

    const REPORT_TYPES = [      
        self::REPORT_TYPE_ALL =>  ['id' => null, 'filter' => 1, 'input' => 0, 'label' => 'Todos',                        'icon' => 'mdi-vector-union'],
        self::REPORT_TYPE_CUBE =>  ['id' => self::REPORT_TYPE_CUBE, 'filter' => 1, 'input' => 1,  'label' => 'Cubo',     'icon' => 'mdi-cube-outline'],
        self::REPORT_TYPE_DASH =>  ['id' => self::REPORT_TYPE_DASH, 'filter' => 1, 'input' => 1,  'label' => 'DashBoard','icon' => 'mdi-monitor-dashboard'],
        self::REPORT_TYPE_EXCEL =>  ['id' => self::REPORT_TYPE_EXCEL, 'filter' => 1, 'input' => 1, 'label' => 'Excel',    'icon' => 'mdi-microsoft-excel'],
        self::REPORT_TYPE_MAILING =>  ['id' => self::REPORT_TYPE_MAILING, 'filter' => 1, 'input' => 1, 'label' => 'Mailing','icon' => 'mdi-format-list-numbered'],
    ];

    const INTERVAL_DAILY = 0;
    const INTERVAL_WEEKLY = 1;
    const INTERVAL_BIWEEKLY = 2;
    const INTERVAL_MONTHLY = 3;

    const INTERVALS_DAILY = [
        0 => ['id' => 0, 'label' => 'Todos - Diário'],
        1 => ['id' => 1, 'label' => 'Todos - Hora-Hora'],
    ];

    const INTERVALS_WEEK_DAYS = [
        0 => ['id' => 2, 'label' => 'Segunda'],
        1 => ['id' => 3, 'label' => 'Terça'],
        2 => ['id' => 4, 'label' => 'Quarta'],
        3 => ['id' => 5, 'label' => 'Quinta'],
        4 => ['id' => 6, 'label' => 'Sexta'],
    ];

    const INTERVALS_MONTH_DAYS = [
        0 => ['id' => 1, 'label' => 1],
        1 => ['id' => 2, 'label' => 2],
        2 => ['id' => 3, 'label' => 3],
        3 => ['id' => 4, 'label' => 4],
        4 => ['id' => 5, 'label' => 5],
        5 => ['id' => 6, 'label' => 6],
        6 => ['id' => 7, 'label' => 7],
        7 => ['id' => 8, 'label' => 8],
        8 => ['id' => 9, 'label' => 9],
        9 => ['id' => 10, 'label' => 10],
        10 => ['id' => 11, 'label' => 11],
        11 => ['id' => 12, 'label' => 12],
        12 => ['id' => 13, 'label' => 13],
        13 => ['id' => 14, 'label' => 14],
        14 => ['id' => 15, 'label' => 15],
        15 => ['id' => 16, 'label' => 16],
        16 => ['id' => 17, 'label' => 17],
        17 => ['id' => 18, 'label' => 18],
        18 => ['id' => 19, 'label' => 19],
        19 => ['id' => 20, 'label' => 20],
        20 => ['id' => 20, 'label' => 20],
        20 => ['id' => 21, 'label' => 21],
        21 => ['id' => 22, 'label' => 22],
        22 => ['id' => 23, 'label' => 23],
        23 => ['id' => 24, 'label' => 24],
        24 => ['id' => 25, 'label' => 25],
        25 => ['id' => 26, 'label' => 26],
        26 => ['id' => 27, 'label' => 27],
        27 => ['id' => 28, 'label' => 28],
        28 => ['id' => 29, 'label' => 29],
        29 => ['id' => 30, 'label' => 30],       
        30 => ['id' => 31, 'label' => 31]       
    ];

    const INTERVALS = [
        self::INTERVAL_DAILY =>  ['id' => self::INTERVAL_DAILY, 'label' => 'Diário',  'data' => self::INTERVALS_DAILY],
        self::INTERVAL_WEEKLY =>  ['id' => self::INTERVAL_WEEKLY, 'label' => 'Semanal',  'data' => self::INTERVALS_WEEK_DAYS],
        self::INTERVAL_BIWEEKLY =>  ['id' => self::INTERVAL_BIWEEKLY, 'label' => 'Quinzenal',  'data' =>  self::INTERVALS_MONTH_DAYS],
        self::INTERVAL_MONTHLY =>  ['id' => self::INTERVAL_MONTHLY, 'label' => 'Mensal',  'data' => self::INTERVALS_MONTH_DAYS],
    ];

    const GROUP_ALL = 0;    
    const GROUP_CORPORATE = 1;     
    const GROUP_MULTISECTOR = 2;
    const GROUP_PERFORMANCE = 3;
    const GROUP_PAP = 4; 
    const GROUP_PARTNER_A = 5;
    const GROUP_PARTNER_B = 6;

    const GROUPS = [      
        self::GROUP_ALL =>  ['id' => self::GROUP_ALL, 'label' => 'Todos'],
        self::GROUP_CORPORATE =>  ['id' => self::GROUP_CORPORATE, 'label' => 'Corporativo'],
        self::GROUP_MULTISECTOR =>  ['id' => self::GROUP_MULTISECTOR, 'label' => 'Multisetor'],
        self::GROUP_PERFORMANCE =>  ['id' => self::GROUP_PERFORMANCE, 'label' => 'Performance'],
        self::GROUP_PAP =>  ['id' => self::GROUP_PAP, 'label' => 'PAP'],
        self::GROUP_PARTNER_A =>  ['id' => self::GROUP_PARTNER_A, 'label' => 'Parceiro A'],
        self::GROUP_PARTNER_B =>  ['id' => self::GROUP_PARTNER_B, 'label' => 'Parceiro B'],
    ];

    const GOUPS_DEFAULT = [
        'ext' => self::GROUP_PARTNER_A,
        'prb' => self::GROUP_PARTNER_B,
        'usr' => self::GROUP_ALL,
    ];
}
