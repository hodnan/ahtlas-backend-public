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
        Schema::connection('modules')->create('kpi_result_employees', function (Blueprint $table) {
            $table->id();
            $table->timestamp('month_ref');
            $table->BigInteger('indicator_id');
            $table->unsignedBigInteger('sector_n1_id');
            $table->unsignedBigInteger('sector_n2_id');
            $table->string('username', 20);
            $table->BigInteger('hierarchical_level');
            $table->float('factor_0');
            $table->float('factor_1');
            $table->float('factor_2')->nullable();
            $table->float('result')->nullable();
            $table->float('avg')->nullable();
            $table->boolean('consolidated');
            $table->timestamps();

            $table->unique(['month_ref', 'sector_n1_id', 'sector_n2_id', 'indicator_id', 'username', 'hierarchical_level', 'consolidated'], 'kpi_result_employees_un');
            $table->index('month_ref');
            $table->index('indicator_id');
            $table->index('sector_n1_id');
            $table->index('sector_n2_id');
            $table->index('username');
            $table->index('hierarchical_level');
            $table->index('consolidated');
           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('modules')->dropIfExists('kpi_result_employees');
    }
};
