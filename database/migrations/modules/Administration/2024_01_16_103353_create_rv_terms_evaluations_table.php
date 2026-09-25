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
        Schema::connection('modules')->create('rv_terms_evaluations', function (Blueprint $table) {
            $table->id();
            $table->date('month_ref');
            $table->unsignedBigInteger('term_id');
            $table->unsignedBigInteger('sector_n1_id');
            $table->unsignedBigInteger('sector_n2_id');
            $table->unsignedBigInteger('indicator_id');            
            $table->integer('position')->nullable();              
            $table->string('username',20)->nullable();              
            $table->string('owner_bi',20)->nullable();           
            $table->string('owner_rv',20)->nullable();           
            $table->integer('directive_type');
            $table->integer('directive_range')->nullable();           
            $table->string('directive_operation',3)->nullable();;
            $table->float('directive_target')->nullable();
            $table->integer('directive_target_type')->nullable();
            $table->float('directive_value')->nullable();
            $table->float('result_target')->nullable();
            $table->float('result_value')->nullable();
            $table->date('result_max_date')->nullable();  
            $table->float('evaluation_value')->nullable();
            $table->json('evaluation_details')->nullable();  
            $table->timestamps();

            $table->unique([
                'month_ref',
                'term_id',
                'sector_n1_id',
                'sector_n2_id',
                'indicator_id',
                'username',
                'directive_type' 
            ], 'un_rv_term_eva');

            $table->index('month_ref');
            $table->index('term_id');
            $table->index('sector_n1_id');
            $table->index('sector_n2_id');
            $table->index('indicator_id');
            $table->index('position');
            $table->index('owner_bi');
            $table->index('owner_rv');
            $table->index('directive_type');
            $table->index('directive_operation');
            $table->index('directive_target_type');
            $table->index('result_max_date');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('modules')->dropIfExists('rv_terms_evaluations');
    }
};
