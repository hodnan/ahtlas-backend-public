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
        Schema::connection('mis_primary')->create('DB_MIS.dbo.TB_TEL_DIME_SLA_P1_AHTLAS_TMP', function (Blueprint $table) {
            $table->id();
            $table->string('CARGA_ID', 50)->nullable();
            $table->string('CD_DATA', 50)->nullable();
            $table->string('CD_HORA', 50)->nullable();
            $table->string('NO_SITE', 50)->nullable();
            $table->string('CD_SETOR', 50)->nullable();
            $table->string('PREV_REC', 50)->nullable();
            $table->string('PREV_ATE', 50)->nullable();
            $table->string('PREV_TMA', 50)->nullable();
            $table->string('PREV_NS', 50)->nullable();
            $table->string('PREV_HC', 50)->nullable();
            $table->string('PREV_HC_PAUSA', 50)->nullable();
            $table->string('PREV_DN', 50)->nullable();
            $table->string('PESO_DIA', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mis_primary')->dropIfExists('DB_MIS.dbo.TB_TEL_DIME_SLA_P1_AHTLAS_TMP');
    }
};
