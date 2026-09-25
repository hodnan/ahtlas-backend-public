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
        Schema::connection('modules')->create('employee_sectors_n1_n2', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->bigInteger('sector_n1_id');
            $table->bigInteger('sector_n2_id');   
            $table->boolean('active')->default(1);  
            $table->timestamps(); 

            // índices
            $table->index('sector_n1_id');
            $table->index('sector_n2_id');
            $table->index('active');

            $table->unique(['sector_n1_id', 'sector_n2_id'], 'employee_sectors_n1_n2_un');            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('modules')->dropIfExists('employee_sectors_n1_n2');
    }
};
