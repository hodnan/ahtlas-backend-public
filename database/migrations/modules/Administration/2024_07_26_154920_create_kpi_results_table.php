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
        Schema::connection('modules')->create('kpi_results', function (Blueprint $table) {
            $table->timestamp('date_ref');
            $table->unsignedBigInteger('sector_n1_id');
            $table->BigInteger('indicator_id');
            $table->string('username', 20);
            $table->float('factor_0');
            $table->float('factor_1');
            $table->float('factor_2');
            $table->float('result');
            $table->timestamps();

            $table->primary(['date_ref', 'sector_n1_id', 'indicator_id', 'username'], 'un_per_kpi_res');

            $table->index('date_ref');
            $table->index('sector_n1_id');
            $table->index('indicator_id');
            $table->index('username');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('modules')->dropIfExists('modules.kpi_results');
    }
};
