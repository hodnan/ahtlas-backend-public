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
        Schema::connection('modules')->create('rv_terms_payments', function (Blueprint $table) {
            $table->id();
            $table->string('event');
            $table->boolean('taxation')->nullable();
        
            $table->string('created_by', 20);        
            $table->string('updated_by', 20)->nullable();            
            $table->timestamps();
        });
    }
  
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('modules')->dropIfExists('rv_terms_payments');
    }
};
