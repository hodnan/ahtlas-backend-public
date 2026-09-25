<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the MIGRACAOs.
     */
    public function up(): void
    {
        Schema::connection('mis_primary')->create('DB_RH.dbo.TB_RH_HEAD_COUNT_FT', function (Blueprint $table) {
            $table->id();
            $table->integer('CD_DATA');
            $table->integer('CD_SETOR');
            $table->integer('CD_MAT_DIRETOR')->nullable();
            $table->integer('CD_MAT_GERENTE_RELAC')->nullable();
            $table->integer('CD_MAT_GERENTE_LOCAL')->nullable();
            $table->float('NU_HC_DIM')->nullable();
            $table->float('NU_FERIAS_DIM')->nullable();
            $table->float('NU_TREINAMENTO_DIM')->nullable();
            $table->float('NU_TREINAMENTO_INICIAL_DIM')->nullable();
            $table->float('NU_TREINAMENTO_MIGRACAO_DIM')->nullable();
            $table->float('NU_TOTAL_DIM')->nullable();
            $table->float('NU_HC_REAL')->nullable();
            $table->float('NU_FERIAS_REAL')->nullable();
            $table->float('NU_TREINAMENTO_REAL')->nullable();
            $table->float('NU_TREINAMENTO_INICIAL_REAL')->nullable();
            $table->float('NU_TREINAMENTO_MIGRACAO_REAL')->nullable();
            $table->float('NU_TREINAMENTO_RECICLAGEM_REAL')->nullable();
            $table->float('NU_TREINAMENTO_RETORNO_REAL')->nullable();
            $table->float('NU_AFASTADO_REAL')->nullable();

            $table->float('NU_TO_TOTAL_REAL')->nullable();
            $table->float('NU_TO_ATIVO_REAL')->nullable();
            $table->float('NU_TO_OPERACAO_REAL')->nullable();
            $table->float('NU_TO_TREINAMENTO_REAL')->nullable();

            $table->float('NU_TOTAL_REAL')->nullable();
            $table->float('NU_TOTAL_ATIVO_REAL')->nullable();
            $table->float('NU_HD_CLIENT_DIM')->nullable();
            $table->float('NU_HC_DIF')->nullable();
            $table->float('NU_TOTAL_DIF')->nullable();
            $table->float('NU_FERIAS_DIF')->nullable();
            $table->float('NU_TREINAMENTO_DIF')->nullable();

            $table->float('NU_TREINAMENTO_PREV_NESTE_MES')->nullable();
            $table->float('NU_TREINAMENTO_PREV_PROXIMO_MES')->nullable();
            
            $table->integer('NU_VERSAO');

            $table->unique(['CD_DATA', 'CD_SETOR'], "HEAD_COUNT_UN");

            // $table->index([
            //     'CD_DATA',
            //     'CD_SETOR',
            //     'CD_MAT_DIRETOR',
            //     'CD_MAT_GERENTE_RELAC',
            //     'CD_MAT_GERENTE_LOCAL'
            // ], 'HEAD_COUNT_INDEX');

            $table->index('CD_DATA');
            $table->index('CD_SETOR');
            $table->index('CD_MAT_DIRETOR');
            $table->index('CD_MAT_GERENTE_RELAC');
            $table->index('CD_MAT_GERENTE_LOCAL');
        });
    }

    /**
     * Reverse the MIGRACAOs.
     */
    public function down(): void
    {
        Schema::connection('mis_primary')->dropIfExists('DB_RH.dbo.TB_RH_HEAD_COUNT_FT');
    }
};
