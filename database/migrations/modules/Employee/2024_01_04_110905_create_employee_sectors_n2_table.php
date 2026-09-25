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
        Schema::connection('modules')->create('employee_sectors_n2', function (Blueprint $table) {
            $table->bigInteger('id', false)->primary();
            $table->string('name');         
            $table->boolean('active')->default(1);
            $table->timestamps();           

            // índices        
            $table->index('name');            
            $table->index('active');            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('modules')->dropIfExists('employee_sectors_n2');
    }
};
