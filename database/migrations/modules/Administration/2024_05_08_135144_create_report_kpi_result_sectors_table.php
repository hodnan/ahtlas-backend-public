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
        Schema::connection('modules')->create('kpi_result_sectors', function (Blueprint $table) {
            $table->id();
            $table->timestamp('date_ref');
            $table->BigInteger('indicator_id');
            $table->unsignedBigInteger('sector_n1_id');
            $table->float('factor_0');
            $table->float('factor_1');
            $table->float('factor_2')->nullable();
            $table->float('result')->nullable();
            $table->float('avg')->nullable();
            $table->timestamps();

            $table->unique(['date_ref', 'sector_n1_id', 'indicator_id'], 'kpi_result_sectors_unig');
            $table->index('date_ref');
            $table->index('indicator_id');
            $table->index('sector_n1_id');
           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('modules')->dropIfExists('kpi_result_sectors');
    }
};
