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
        Schema::connection('modules')->create('employee_sectors_n1', function (Blueprint $table) {
            $table->BigInteger('id')->primary();
            $table->string('name');
            $table->string('uf', 5);
            $table->integer('hc')->nullable();
            $table->boolean('active')->default(1);
            $table->timestamps();
            // índices
            $table->index(['name', 'uf']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('modules')->dropIfExists('employee_sectors_n1');
    }
};
