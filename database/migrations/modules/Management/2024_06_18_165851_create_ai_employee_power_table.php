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
        Schema::connection('modules')->create('ai_employee_powers', function (Blueprint $table) {
            $table->id();
            $table->timestamp('month_ref');
            $table->string('username',0);
            $table->bigInteger('sector_n1_id')->nullable();
            $table->string('manager_n1_id')->nullable();
            $table->string('manager_n2_id')->nullable();
            $table->string('manager_n3_id')->nullable();
            $table->string('manager_n4_id')->nullable();
            $table->string('manager_n5_id')->nullable();
            $table->integer('hierarchical_level');
            $table->integer('m0');
            $table->integer('m1');
            $table->integer('m2');
            $table->unsignedBigInteger('action_id');
            $table->timestamps();

            $table->index('month_ref');
            $table->index('username');
            $table->index('sector_n1_id');
            $table->index('manager_n1_id');
            $table->index('manager_n2_id');
            $table->index('manager_n3_id');
            $table->index('manager_n4_id');
            $table->index('manager_n5_id');
            $table->index('hierarchical_level');
            $table->index('m0');
            $table->index('m1');
            $table->index('m2');
            $table->index('action_id');
        });

       
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('modules')->dropIfExists('ai_employee_powers');
    }
};
