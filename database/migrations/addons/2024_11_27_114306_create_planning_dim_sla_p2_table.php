<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('mis_primary')->create('DB_MIS.dbo.TB_TEL_DIME_SLA_P2_AHTLAS_TMP', function (Blueprint $table) {
            $table->id();
            $table->string('ANO', 50)->nullable();
            $table->string('MES', 50)->nullable();
            $table->string('DATA', 50)->nullable();
            $table->string('CD_DPTO', 50)->nullable();
            $table->string('CD_TTV', 50)->nullable();
            $table->string('DIM', 50)->nullable();
            $table->string('DIM_FERIAS', 50)->nullable();
            $table->string('DIM_FTE', 50)->nullable();
            $table->string('PA', 50)->nullable();
            $table->string('DIM_PRO_RATA', 50)->nullable();
            $table->string('DIM_PRO_RATA_FERIAS', 50)->nullable();
            $table->string('VERSAO_CAPACITY', 50)->nullable();
            $table->string('PER_ESCALA', 50)->nullable();
            $table->string('AG_4HS', 50)->nullable();
            $table->string('AG_6HS', 50)->nullable();
            $table->string('AG_7HS', 50)->nullable();
            $table->string('HC_CLIENTE', 50)->nullable();
            $table->string('FTE_AJUSTADO', 50)->nullable();
            $table->string('TX_OCUP', 50)->nullable();
            $table->string('CARGA_ID', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mis_primary')->dropIfExists('DB_MIS.dbo.TB_TEL_DIME_SLA_P2_AHTLAS_TMP');
    }
};
