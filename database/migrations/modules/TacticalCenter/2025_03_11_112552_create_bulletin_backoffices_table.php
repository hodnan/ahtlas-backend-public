<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('modules')->create('bulletin_backoffices', function (Blueprint $table) {
            $table->id();           
            $table->date('month_ref');
            $table->date('date_ref');
            $table->time('time_start');
            $table->time('time_end');
            $table->string('username',20);
            $table->bigInteger('sector_n1_id')->nullable();
            $table->string('manager_n1_id',20)->nullable();
            $table->string('manager_n2_id',20)->nullable();
            $table->string('manager_n3_id',20)->nullable();
            $table->string('manager_n4_id',20)->nullable();
            $table->string('manager_n5_id',20)->nullable();
            $table->time('working_hours')->nullable();
            $table->bigInteger('protocol');
            $table->bigInteger('environment_id');
            $table->bigInteger('mailing_id');
            $table->bigInteger('queue_id');
            $table->integer('unique')->nullable();
            $table->integer('finished')->nullable();            
            $table->bigInteger('time_productive')->nullable();
            $table->bigInteger('time_handle')->nullable();         
            $table->integer('is_simultaneous')->nullable();         
            $table->integer('status_id');
            $table->timestamps();

            $table->index('month_ref');
            $table->index('date_ref');
            $table->index('username');
            $table->index('sector_n1_id');
            $table->index('manager_n1_id');
            $table->index('manager_n2_id');
            $table->index('manager_n3_id');
            $table->index('manager_n4_id');
            $table->index('manager_n5_id');
            $table->index('protocol');
            $table->index('environment_id');
            $table->index('mailing_id');
            $table->index('queue_id');
            $table->index('time_start');
            $table->index('time_end');
            $table->index('status_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('modules')->dropIfExists('bulletin_backoffices');
    }
};
