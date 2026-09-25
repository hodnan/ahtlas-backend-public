<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('modules')->create('bulletin_hh_dates', function (Blueprint $table) {
            $table->timestamp('dt_data');
            $table->string('no_intervalo', 10);
            $table->string('no_sigla', 5);
            $table->float('nu_setor', 20, 8);
            $table->float('nu_prev_recebidas', 20, 8);
            $table->float('nu_prev_atendidas', 20, 8);
            $table->float('nu_prev_ns_ponderado', 20, 8);
            $table->float('nu_prev_tp_falado', 20, 8);
            $table->float('nu_prev_tp_logado', 20, 8);
            $table->float('nu_prev_pausas', 20, 8);
            $table->float('nu_prev_carga_h', 20, 8);
            $table->float('nu_prev_tp_corte', 20, 8);
            $table->float('nu_prev_dn', 20, 8);
            $table->float('nu_real_recebidas', 20, 8);
            $table->float('nu_real_atendidas', 20, 8);
            $table->float('nu_real_abandonadas', 20, 8);
            $table->float('nu_real_tp_falado', 20, 8);
            $table->float('nu_real_tp_espera', 20, 8);
            $table->float('nu_real_tp_logado', 20, 8);
            $table->float('nu_real_pausas', 20, 8);
            $table->float('nu_real_1_ch_ns', 20, 8);
            $table->float('nu_real_2_ch_ns', 20, 8);
            $table->float('nu_real_tp_falado_r', 20, 8);
            $table->float('nu_real_pausa_tp_lanche', 20, 8);
            $table->float('nu_real_pausa_tp_refeicao', 20, 8);
            $table->float('nu_real_pausa_tp_descanso', 20, 8);
            $table->float('nu_real_pausa_tp_banheiro', 20, 8);
            $table->float('nu_real_pausa_tp_feedback', 20, 8);
            $table->float('nu_real_pausa_tp_feedback_sup', 20, 8);
            $table->float('nu_real_pausa_tp_reuniao', 20, 8);
            $table->float('nu_real_pausa_tp_input_vendas', 20, 8);
            $table->float('nu_real_pausa_tp_treinamento', 20, 8);
            $table->float('nu_real_pausa_tp_callback', 20, 8);
            $table->float('nu_real_pausa_tp_comunicacao', 20, 8);
            $table->float('nu_real_pausa_tp_backoffice', 20, 8);
            $table->float('nu_real_pausa_tp_defeito', 20, 8);
            $table->float('nu_real_pausa_tp_logof', 20, 8);
            $table->float('nu_real_pausa_tp_exame_periodico', 20, 8);
            $table->float('nu_prev_recebidas_fechado', 20, 8);
            $table->float('nu_prev_ns_ponderado_fechado', 20, 8);

            $table->primary(['dt_data', 'nu_setor']);
            $table->index('no_sigla');
            $table->index('dt_data');
            $table->index('no_intervalo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('modules')->dropIfExists('bulletin_hh_dates');
    }
};
