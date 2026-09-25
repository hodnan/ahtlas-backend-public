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
        Schema::connection('modules')->create('employees', function (Blueprint $table) {

            $table->string('username', 20)->unique();
            $table->string('name');
            $table->string('nickname')->nullable();
            $table->binary('avatar')->nullable();
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
            $table->date('birthdate')->nullable();
            $table->boolean('is_veteran')->nullable();
            $table->time('start_time')->nullable();
            $table->time('working_hours')->nullable();
            $table->boolean('active');
            $table->string('status');

            // Índices         
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
            $table->index('admission');
            $table->index('dismissal');
            $table->index('birthdate');
            $table->index('hierarchical_level');
            $table->index('type');
            $table->index('active');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('modules')->dropIfExists('employees');
    }
};
