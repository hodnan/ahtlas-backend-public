<?php

namespace App\Models\Modules\TacticalCenter\Bulletin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Modules\Employee\SectorN1;
use App\Models\Modules\Employee\SectorN1Manager;

class HourHourDate extends Model
{
    use HasFactory;

    protected $connection = 'modules';

    protected $table = 'bulletin_hh_dates';

    public $incrementing = false;

    protected function casts(): array
    {
        return [
            'dt_data' => 'datetime:Y-m-d',
            'no_intervalo' => 'datetime:H:i',
            'nu_setor' => 'integer',
            'nu_prev_recebidas' => 'integer',
            'nu_prev_atendidas' => 'integer',
            'nu_prev_ns_ponderado' => 'integer',
            'nu_prev_tp_falado' => 'integer',
            'nu_prev_tp_logado' => 'integer',
            'nu_prev_pausas' => 'integer',
            'nu_prev_carga_h' => 'integer',
            'nu_prev_tp_corte' => 'integer',
            'nu_prev_dn' => 'integer',
            'nu_real_recebidas' => 'integer',
            'nu_real_atendidas' => 'integer',
            'nu_real_abandonadas' => 'integer',
            'nu_real_tp_falado' => 'integer',
            'nu_real_tp_espera' => 'integer',
            'nu_real_tp_logado' => 'integer',
            'nu_real_pausas' => 'integer',
            'nu_real_1_ch_ns' => 'integer',
            'nu_real_2_ch_ns' => 'integer',
            'nu_real_tp_falado_r' => 'integer',
            'nu_real_pausa_tp_lanche' => 'integer',
            'nu_real_pausa_tp_refeicao' => 'integer',
            'nu_real_pausa_tp_descanso' => 'integer',
            'nu_real_pausa_tp_banheiro' => 'integer',
            'nu_real_pausa_tp_feedback' => 'integer',
            'nu_real_pausa_tp_feedback_sup' => 'integer',
            'nu_real_pausa_tp_reuniao' => 'integer',
            'nu_real_pausa_tp_input_vendas' => 'integer',
            'nu_real_pausa_tp_treinamento' => 'integer',
            'nu_real_pausa_tp_callback' => 'integer',
            'nu_real_pausa_tp_comunicacao' => 'integer',
            'nu_real_pausa_tp_backoffice' => 'integer',
            'nu_real_pausa_tp_defeito' => 'integer',
            'nu_real_pausa_tp_logof' => 'integer',
            'nu_real_pausa_tp_exame_periodico' => 'integer',
            'nu_prev_recebidas_fechado' => 'integer',
            'nu_prev_ns_ponderado_fechado' => 'integer',
        ];
    }

    protected $appends = [
        'service_level',
        'tma',
        'traffic',
        'service_level',
        'volume',
        'login',
        'breaks',
    ];

    public function sectorN1()
    {
        return $this->belongsTo(SectorN1::class, 'nu_setor', 'id')->select(['name', 'uf', 'id']);
    }

    public function managers()
    {
        return $this->hasMany(SectorN1Manager::class, 'sector_n1_id', 'nu_setor')->select(['manager_username', 'sector_n1_id']);
    }


    public function getServiceLevelAttribute()
    {
        $scaledGeneral = (float) $this->attributes['nu_prev_recebidas_fechado'] == 0 ? null : ((float) $this->attributes['nu_prev_ns_ponderado_fechado'] / (float) $this->attributes['nu_prev_recebidas_fechado']) * 100;
        $scaledPartial = (float) $this->attributes['nu_prev_recebidas'] == 0 ? null : ((float) $this->attributes['nu_prev_ns_ponderado'] / (float) $this->attributes['nu_prev_recebidas']) * 100;
        $realGeneral = (float) $this->attributes['nu_real_1_ch_ns'] == 0 ? null : ((float) $this->attributes['nu_real_2_ch_ns'] / (float) $this->attributes['nu_real_1_ch_ns']) * 100;

        $difference = (float) $this->attributes['nu_prev_atendidas'] == 0 ? null : ($realGeneral - $scaledPartial);

        $data = [
            'scaled_general' => [
                'label' => 'NS - Dimensionado',
                'brief' => 'Dim.',
                'value' => (float) number_format((float)$scaledGeneral, 2, '.', ''),
            ],
            'scaled_partial' => [
                'label' => 'NS - Ponderado',
                'brief' => 'Pon.',
                'value' => (float) number_format((float)$scaledPartial, 2, '.', ''),
            ],
            'real_general' => [
                'label' => 'NS - Real',
                'brief' => 'Real',
                'value' => (float) number_format((float)$realGeneral, 2, '.', ''),
            ],
            'difference' => [
                'label' => 'NS - Diferênça',
                'brief' => 'Dif.',
                'value' => (float) number_format((float)$difference, 2, '.', ''),
            ],
        ];

        return $data;
    }

    public function getTmaAttribute()
    {
        $scaled = (float) $this->attributes['nu_prev_atendidas'] == 0 ? null : $this->attributes['nu_prev_tp_falado'] / $this->attributes['nu_prev_atendidas'];
        $realGeneral = (float) $this->attributes['nu_real_atendidas'] == 0 ? null : $this->attributes['nu_real_tp_falado'] / $this->attributes['nu_real_atendidas'];
        $realReceptive = (float) $this->attributes['nu_real_atendidas'] == 0 ? null : $this->attributes['nu_real_tp_falado_r'] / $this->attributes['nu_real_atendidas'];
        $difference = (float) $scaled == 0 ? null : (($realGeneral - $scaled) / $scaled) * 100;

        $data = [
            'scaled' => [
                'label' => 'TMA - Dimensionado',
                'brief' => 'Dim.',
                'value' => (int) round($scaled),
            ],
            'real_general' => [
                'label' => 'TMA - Geral',
                'brief' => 'Geral',
                'value' => (int) round($realGeneral)
            ],
            'real_receptive' => [
                'label' => 'TMA - Receptivo',
                'brief' => 'Recep.',
                'value' => (int) round($realReceptive)
            ],
            'difference' => [
                'label' => 'TMA - Diferênça',
                'simbol' => '%',
                'brief' => 'Dif.',
                'value' => (float) number_format((float)$difference, 2, '.', '')
            ],
        ];

        return $data;
    }

    public function getVolumeAttribute()
    {
        $scaledCall = $this->attributes['nu_prev_recebidas'];
        $realCallReceived = $this->attributes['nu_real_recebidas'];
        $realCallAnswered = $this->attributes['nu_real_atendidas'];
        $differenceCall = (float) $scaledCall == 0 ? null : (($realCallReceived - $scaledCall) / $scaledCall) * 100;

        $realAbandoned = $this->attributes['nu_real_abandonadas'];
        $differenceAbandoned = (float) $realCallReceived == 0 ? null : ($realAbandoned / $realCallReceived) * 100;

        $realTme = (float) $realCallReceived == 0 ? null : $this->attributes['nu_real_tp_espera'] / $realCallReceived;

        $scaledDn = $this->attributes['nu_prev_dn'];
        $differenceDn = (float) $scaledDn == 0 ? null : ($realCallReceived / $scaledDn) * 100;

        $data = [
            'scaled_call' => [
                'label' => 'Recebidas - Dimensionado',
                'brief' => 'Dim.',
                'value' => (int) round($scaledCall),
            ],
            'real_call_received' => [
                'label' => 'Recebidas - Real',
                'brief' => 'Real',
                'value' => (int) round($realCallReceived),
            ],
            'real_call_answered' => [
                'label' => 'Atendidas',
                'brief' => 'Atd.',
                'value' => (int) round($realCallAnswered),
            ],
            'difference_call' => [
                'label' => 'Recebidas - Diferênça',
                'brief' => 'Dif.',
                'simbol' => '%',
                'value' => (float) number_format((float)$differenceCall, 2, '.', ''),
            ],
            'real_abandoned' => [
                'label' => 'Abandonadas',
                'brief' => 'Abn.',
                'value' => (int) round($realAbandoned),
            ],
            'difference_abandoned' => [
                'label' => '% Abandonadas',
                'brief' => 'Abn.',
                'simbol' => '%',
                'value' => (float) number_format((float)$differenceAbandoned, 2, '.', ''),
            ],
            'real_tme' => [
                'label' => 'TME',
                'brief' => 'TME',
                'value' => (int) round($realTme),
            ],
            'scaled_dn' => [
                'label' => 'DN (Dia Normal)',
                'brief' => 'DN',
                'value' => (int) round($scaledDn),
            ],
            'difference_dn' => [
                'label' => 'DN (Dia Normal) - Diferênça',
                'brief' => '% DN',
                'simbol' => '%',
                'value' => (float) number_format((float)$differenceDn, 2, '.', ''),
            ],

        ];

        return $data;
    }

    public function getTrafficAttribute()
    {
        $scaled = (float) $this->attributes['nu_prev_tp_falado'] == 0 ? null : $this->attributes['nu_prev_tp_falado'];
        $realGeneral = (float) $this->attributes['nu_real_atendidas'] == 0 ? null : ($this->attributes['nu_real_tp_falado'] / $this->attributes['nu_real_atendidas']) * $this->attributes['nu_real_recebidas'];
        $difference = (float)  $scaled == 0 ? null : (($realGeneral - $scaled) / $scaled) * 100;
        $realMet = (int) $this->attributes['nu_real_tp_falado'];

        $data = [
            'scaled' => [
                'label' => 'Tráfego - Dimensionado',
                'brief' => 'Dim.',
                'value' => (int) round($scaled),
            ],
            'real_general' => [
                'label' => 'Tráfego - Entrante',
                'brief' => 'Ent.',
                'value' => (int) round($realGeneral),
            ],
            'difference' => [
                'label' => 'Tráfego - Diferênça',
                'brief' => '% Ent.',
                'simbol' => '%',
                'value' => (float) number_format((float)$difference, 2, '.', ''),
            ],
            'real_met' => [
                'label' => 'Tráfego - Atendidas',
                'brief' => 'Atd',
                'value' => (int) round($realMet),
            ],

        ];

        return $data;
    }

    public function getLoginAttribute()
    {
        $scaledAgent = (float) $this->attributes['nu_prev_carga_h'] == 0 ? null : $this->attributes['nu_prev_tp_logado'] / $this->attributes['nu_prev_carga_h'];
        $scaledTime = $this->attributes['nu_prev_tp_logado'];
        $scaledBreak = (float) $this->attributes['nu_prev_tp_logado'] == 0 ? null : $this->attributes['nu_prev_pausas'] / $this->attributes['nu_prev_tp_logado'];
        $scaledAttended = $this->attributes['nu_prev_tp_logado'] - $this->attributes['nu_prev_pausas'];

        $realAgent = (float) $this->attributes['nu_prev_carga_h'] == 0 ? null : $this->attributes['nu_real_tp_logado'] / $this->attributes['nu_prev_carga_h'];
        $realTime = $this->attributes['nu_real_tp_logado'];
        $realBreak = (float) $this->attributes['nu_real_tp_logado'] == 0 ? null : $this->attributes['nu_real_pausas'] / $this->attributes['nu_real_tp_logado'];
        $realAttended = $this->attributes['nu_real_tp_logado'] - $this->attributes['nu_real_pausas'];

        $differenceAgent = (float) $scaledAgent == 0 ? null : (($realAgent - $scaledAgent) / $scaledAgent) * 100;
        $differenceTime = (float) $scaledTime == 0 ? null : (($realTime - $scaledTime) / $scaledTime) * 100;
        $differenceBreak = (float) $scaledBreak == 0 ? null : (($realBreak - $scaledBreak) / $scaledBreak) * 100;
        $differenceAttended = (float) $scaledAttended == 0 ? null : (($realAttended - $scaledAttended) / $scaledAttended) * 100;

        $data = [
            // Agentes
            'scaled_agent' => [
                'label' => 'Agentes - Dimensionado',
                'brief' => 'Ag. D',
                'value' => (float) round($scaledAgent),
            ],
            'real_agent' => [
                'label' => 'Agentes - Real',
                'brief' => 'Ag. R',
                'value' => (float) round($realAgent),
            ],
            'difference_agent' => [
                'label' => 'Agentes - Diferênça',
                'brief' => '% Ag.',
                'simbol' => '%',
                'value' => (float) number_format((float)$differenceAgent, 1, '.', ''),
            ],

            // Tempo
            'scaled_time' => [
                'label' => 'Tempo - Dimensionado',
                'brief' => 'Temp. D',
                'value' => (int) $scaledTime,
            ],
            'real_time' => [
                'label' => 'Tempo - Real',
                'brief' => 'Temp. R',
                'value' => (int) $realTime,
            ],
            'difference_time' => [
                'label' => 'Tempo - Diferença',
                'brief' => '% Temp.',
                'simbol' => '%',
                'value' => (float) number_format((float)$differenceTime, 1, '.', ''),
            ],

            // Pausas
            'scaled_break' => [
                'label' => 'Pausas - Dimensionado',
                'brief' => 'Pausas. D',
                'value' => (float) number_format((float)$scaledBreak, 1, '.', ''),
            ],
            'real_break' => [
                'label' => 'Pausas - Real',
                'brief' => 'Pausas. R',
                'value' => (float) number_format((float)$realBreak, 1, '.', ''),
            ],
            'difference_break' => [
                'label' => 'Pausas - Diferença',
                'brief' => '% Pausas',
                'simbol' => '%',
                'value' => (float) number_format((float)$differenceBreak, 1, '.', ''),
            ],

            // Atendidas
            'scaled_attended' => [
                'label' => 'Atendidas - Dimensionado',
                'brief' => 'Atd. D',
                'value' => (int) (float)$scaledAttended,
            ],
            'real_attended' => [
                'label' => 'Atendidas - Real',
                'brief' => 'Atd. D',
                'value' => (int) (float)$realAttended,
            ],
            'difference_attended' => [
                'label' => 'Atendidas - Diferênça',
                'brief' => '% Atd.',
                'value' => (float) number_format((float)$differenceAttended, 1, '.', ''),
            ],
        ];

        return $data;
    }

    public function getBreaksAttribute()
    {
        $scaledBreak = (float) $this->attributes['nu_prev_tp_logado'] == 0 ? null : $this->attributes['nu_prev_pausas'] / $this->attributes['nu_prev_tp_logado'];
        $realBreak = (float) $this->attributes['nu_real_tp_logado'] == 0 ? null : $this->attributes['nu_real_pausas'] / $this->attributes['nu_real_tp_logado'];
        $differenceBreak = (float) $scaledBreak == 0 ? null : (($realBreak - $scaledBreak) / $scaledBreak) * 100;

        $lunch = (float) $this->attributes['nu_real_tp_logado'] == 0 ? null : $this->attributes['nu_real_pausa_tp_lanche'] / $this->attributes['nu_real_tp_logado'];
        $meal = (float) $this->attributes['nu_real_tp_logado'] == 0 ? null : $this->attributes['nu_real_pausa_tp_refeicao'] / $this->attributes['nu_real_tp_logado'];
        $rest = (float) $this->attributes['nu_real_tp_logado'] == 0 ? null : $this->attributes['nu_real_pausa_tp_descanso'] / $this->attributes['nu_real_tp_logado'];
        $bathroom = (float) $this->attributes['nu_real_tp_logado'] == 0 ? null : $this->attributes['nu_real_pausa_tp_banheiro'] / $this->attributes['nu_real_tp_logado'];
        $feedback = (float) $this->attributes['nu_real_tp_logado'] == 0 ? null : $this->attributes['nu_real_pausa_tp_feedback'] / $this->attributes['nu_real_tp_logado'];
        $meeting = (float) $this->attributes['nu_real_tp_logado'] == 0 ? null : $this->attributes['nu_real_pausa_tp_reuniao'] / $this->attributes['nu_real_tp_logado'];
        $exam = (float) $this->attributes['nu_real_tp_logado'] == 0 ? null : $this->attributes['nu_real_pausa_tp_exame_periodico'] / $this->attributes['nu_real_tp_logado'];
        $training = (float) $this->attributes['nu_real_tp_logado'] == 0 ? null : $this->attributes['nu_real_pausa_tp_treinamento'] / $this->attributes['nu_real_tp_logado'];
        $backoffice = (float) $this->attributes['nu_real_tp_logado'] == 0 ? null : $this->attributes['nu_real_pausa_tp_backoffice'] / $this->attributes['nu_real_tp_logado'];
        $defect = (float) $this->attributes['nu_real_tp_logado'] == 0 ? null : $this->attributes['nu_real_pausa_tp_defeito'] / $this->attributes['nu_real_tp_logado'];
        $logof = (float) $this->attributes['nu_real_tp_logado'] == 0 ? null : $this->attributes['nu_real_pausa_tp_logof'] / $this->attributes['nu_real_tp_logado'];
        $communication = (float) $this->attributes['nu_real_tp_logado'] == 0 ? null : $this->attributes['nu_real_pausa_tp_comunicacao'] / $this->attributes['nu_real_tp_logado'];
        $feedbackSup = (float) $this->attributes['nu_real_tp_logado'] == 0 ? null : $this->attributes['nu_real_pausa_tp_feedback_sup'] / $this->attributes['nu_real_tp_logado'];
        $callback = (float) $this->attributes['nu_real_tp_logado'] == 0 ? null : $this->attributes['nu_real_pausa_tp_callback'] / $this->attributes['nu_real_tp_logado'];
        $sales = (float) $this->attributes['nu_real_tp_logado'] == 0 ? null : $this->attributes['nu_real_pausa_tp_input_vendas'] / $this->attributes['nu_real_tp_logado'];

        $data = [
             // Pausas
             'scaled_break' => [
                'label' => 'Pausas - Dimensionado',
                'brief' => 'Pausas. D',
                'value' => (float) number_format((float)$scaledBreak, 4, '.', ''),
            ],
            'real_break' => [
                'label' => 'Pausas - Real',
                'brief' => 'Pausas. R',
                'value' => (float) number_format((float)$realBreak, 4, '.', ''),
            ],
            'difference_break' => [
                'label' => 'Pausas - Diferença',
                'brief' => '% Pausas',
                'simbol' => '%',
                'value' => (float) number_format((float)$differenceBreak, 2, '.', ''),
            ],
            'lunch' => [
                'label' => 'Pausa - Lanche',
                'brief' => 'Lanche',
                'simbol' => '%',
                'value' => (float) number_format((float)$lunch, 2, '.', ''),
            ],
            'meal' => [
                'label' => 'Pausa - Refeição',
                'brief' => 'Refeição',
                'simbol' => '%',
                'value' => (float) number_format((float)$meal, 2, '.', ''),
            ],
            'rest' => [
                'label' => 'Pausa - Descanso',
                'brief' => 'Descanso',
                'simbol' => '%',
                'value' => (float) number_format((float)$rest, 2, '.', ''),
            ],
            'bathroom' => [
                'label' => 'Pausa - Banheiro',
                'brief' => 'Banheiro',
                'simbol' => '%',
                'value' => (float) number_format((float)$bathroom, 2, '.', ''),
            ],
            'feedback' => [
                'label' => 'Pausa - Feedback',
                'brief' => 'Feedback',
                'simbol' => '%',
                'value' => (float) number_format((float)$feedback, 2, '.', ''),
            ],
            'meeting' => [
                'label' => 'Pausa - Reunião',
                'brief' => 'Reunião',
                'simbol' => '%',
                'value' => (float) number_format((float)$meeting, 2, '.', ''),
            ],
            'exam' => [
                'label' => 'Pausa - Exame Periodico',
                'brief' => 'Exame Periodico',
                'simbol' => '%',
                'value' => (float) number_format((float)$exam, 2, '.', ''),
            ],
            'training' => [
                'label' => 'Pausa - Treinamento',
                'brief' => 'Treinamento',
                'simbol' => '%',
                'value' => (float) number_format((float)$training, 2, '.', ''),
            ],
            'backoffice' => [
                'label' => 'Pausa - Backoffice',
                'brief' => 'Backoffice',
                'simbol' => '%',
                'value' => (float) number_format((float)$backoffice, 2, '.', ''),
            ],
            'defect' => [
                'label' => 'Pausa - Defeito',
                'brief' => 'Defeito',
                'simbol' => '%',
                'value' => (float) number_format((float)$defect, 2, '.', ''),
            ],
            'logof' => [
                'label' => 'Pausa - Logof',
                'brief' => 'Logof',
                'simbol' => '%',
                'value' => (float) number_format((float)$logof, 2, '.', ''),
            ],
            'communication' => [
                'label' => 'Pausa - Comunicação',
                'brief' => 'Comunicação',
                'simbol' => '%',
                'value' => (float) number_format((float)$communication, 2, '.', ''),
            ],
            'feedbackSup' => [
                'label' => 'Pausa - Feedback Supervisor',
                'brief' => 'Feedback Supervisor',
                'simbol' => '%',
                'value' => (float) number_format((float)$feedbackSup, 2, '.', ''),
            ],
            'callback' => [
                'label' => 'Pausa - Callback',
                'brief' => 'Callback',
                'simbol' => '%',
                'value' => (float) number_format((float)$callback, 2, '.', ''),
            ],
            'sales' => [
                'label' => 'Pausa - Input Vendas',
                'brief' => 'Input Vendas',
                'simbol' => '%',
                'value' => (float) number_format((float)$sales, 2, '.', ''),
            ],
        ];

        return $data;
    }

    public function getIntraday()
    {

        return HourHourDateHour::where('nu_setor', $this->attributes['nu_setor'])
            ->where('dt_data', $this->attributes['dt_data'])
            ->orderBy('no_intervalo', 'asc')
            ->get()
            
            ->makeHidden([
                'nu_prev_recebidas',
                'nu_prev_atendidas',
                'nu_prev_ns_ponderado',
                'nu_prev_tp_falado',
                'nu_prev_tp_logado',
                'nu_prev_pausas',
                'nu_prev_carga_h',
                'nu_prev_tp_corte',
                'nu_prev_dn',
                'nu_real_recebidas',
                'nu_real_atendidas',
                'nu_real_abandonadas',
                'nu_real_tp_falado',
                'nu_real_tp_espera',
                'nu_real_tp_logado',
                'nu_real_pausas',
                'nu_real_1_ch_ns',
                'nu_real_2_ch_ns',
                'nu_real_tp_falado_r',
                'nu_real_pausa_tp_lanche',
                'nu_real_pausa_tp_refeicao',
                'nu_real_pausa_tp_descanso',
                'nu_real_pausa_tp_banheiro',
                'nu_real_pausa_tp_feedback',
                'nu_real_pausa_tp_feedback_sup',
                'nu_real_pausa_tp_reuniao',
                'nu_real_pausa_tp_input_vendas',
                'nu_real_pausa_tp_treinamento',
                'nu_real_pausa_tp_callback',
                'nu_real_pausa_tp_comunicacao',
                'nu_real_pausa_tp_backoffice',
                'nu_real_pausa_tp_defeito',
                'nu_real_pausa_tp_logof',
                'nu_real_pausa_tp_exame_periodico',
                'nu_prev_recebidas_fechado',
                'nu_prev_ns_ponderado_fechado',
            ])->toArray();
    }

    public function getChartAttribute()
    {
        try {
            $data = HourHourDateHour::where('nu_setor', $this->attributes['nu_setor'])
            ->where('dt_data', $this->attributes['dt_data'])
            ->orderBy('no_intervalo', 'asc')
            ->get()
            ->makeHidden([
                'nu_prev_recebidas',
                'nu_prev_atendidas',
                'nu_prev_ns_ponderado',
                'nu_prev_tp_falado',
                'nu_prev_tp_logado',
                'nu_prev_pausas',
                'nu_prev_carga_h',
                'nu_prev_tp_corte',
                'nu_prev_dn',
                'nu_real_recebidas',
                'nu_real_atendidas',
                'nu_real_abandonadas',
                'nu_real_tp_falado',
                'nu_real_tp_espera',
                'nu_real_tp_logado',
                'nu_real_pausas',
                'nu_real_1_ch_ns',
                'nu_real_2_ch_ns',
                'nu_real_tp_falado_r',
                'nu_real_pausa_tp_lanche',
                'nu_real_pausa_tp_refeicao',
                'nu_real_pausa_tp_descanso',
                'nu_real_pausa_tp_banheiro',
                'nu_real_pausa_tp_feedback',
                'nu_real_pausa_tp_feedback_sup',
                'nu_real_pausa_tp_reuniao',
                'nu_real_pausa_tp_input_vendas',
                'nu_real_pausa_tp_treinamento',
                'nu_real_pausa_tp_callback',
                'nu_real_pausa_tp_comunicacao',
                'nu_real_pausa_tp_backoffice',
                'nu_real_pausa_tp_defeito',
                'nu_real_pausa_tp_logof',
                'nu_real_pausa_tp_exame_periodico',
                'nu_prev_recebidas_fechado',
                'nu_prev_ns_ponderado_fechado',
            ])->toArray();

        foreach ($data as  $item) {
            $label[] = $item['no_intervalo'];
            $service_level_scaled[] = $item['service_level']['scaled_partial']['value'];
            $service_level_real[] = $item['service_level']['real_general']['value'];           
            $call_answered_real[] = $item['volume']['real_call_answered']['value'];            
            $call_received_real[] = $item['volume']['real_call_received']['value'] - $item['volume']['real_call_answered']['value'];           
            $scaled_call[] =  $item['volume']['scaled_call']['value'];            
        }

        $chart = [
            'label' => [
                'label' => 'Intervalo',
                'value' =>   $label,
            ],
            'service_level_scaled' => [
                'label' => 'SLA NS',
                'value' =>   $service_level_scaled,
            ],
            'service_level_real' => [
                'label' => 'NS',
                'value' =>   $service_level_real,
            ],
            'call_answered_real' => [
                'label' => 'Atendidas',
                'value' =>   $call_answered_real,
            ],
            'call_received_real' => [
                'label' => 'Recebidas',
                'value' =>   $call_received_real,
            ],
            'scaled_call' => [
                'label' => 'Dimensionadas',
                'value' =>  $scaled_call,
            ],
        ];

      
       
        } catch (\Throwable $th) {
            $chart = [];
        }
       
        return $chart;
    }
}
