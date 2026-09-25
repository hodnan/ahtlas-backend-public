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
        Schema::connection('modules')->create('employee_dailies', function (Blueprint $table) {
           
            $table->id();
            $table->date('month_ref'); 
            $table->date('date_ref'); 
            $table->string('username', 20);
            $table->string('name');
            $table->string('uf');
            $table->string('position');
            $table->string('position_summary');
            $table->bigInteger('sector_n1_id');
            $table->bigInteger('sector_n2_id')->default(0);
            $table->string('manager_n1_id')->nullable();
            $table->string('manager_n2_id')->nullable();
            $table->string('manager_n3_id')->nullable();
            $table->string('manager_n4_id')->nullable();
            $table->string('manager_n5_id')->nullable();
            $table->bigInteger('hierarchical_level');
            $table->string('type')->nullable();
            $table->boolean('staff')->nullable();
            $table->date('admission')->nullable();
            $table->date('dismissal')->nullable();
            $table->boolean('is_veteran')->nullable();
            $table->time('start_time')->nullable();
            $table->time('working_hours')->nullable();
            $table->bigInteger('training_id')->nullable();
            $table->date('training_start')->nullable();
            $table->date('training_end')->nullable();           
            $table->date('away_start')->nullable();
            $table->date('away_end')->nullable();
            $table->boolean('active');
            $table->string('status');   
            $table->string('status_op');   
            $table->timestamps()  ;

            // Índices
            $table->index('month_ref'); 
            $table->index('date_ref'); 
            $table->index('username');
            $table->index('name');
            $table->index('uf');
            $table->index('position');
            $table->index('position_summary');
            $table->index('sector_n1_id');
            $table->index('sector_n2_id');
            $table->index('manager_n1_id');
            $table->index('manager_n2_id');
            $table->index('manager_n3_id');
            $table->index('manager_n4_id');
            $table->index('manager_n5_id');
            $table->index('hierarchical_level');
            $table->index('type');
            $table->index('active');
            $table->index('status');
            $table->index('status_op');

            $table->unique(['date_ref', 'username'], 'employee_dailies_un');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('modules')->dropIfExists('employee_dailies');
    }
};
