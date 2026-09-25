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
        Schema::connection('modules')->create('trade_times', function (Blueprint $table) {
            $table->id();
            $table->string('username', 20);
            $table->integer('sector_n1_id');
            $table->time('time_old')->nullable();
            $table->string('time_label', 50);
            $table->time('time_start');
            $table->time('time_end');
            $table->longText('notes')->nullable();            
            $table->integer('status')->default(0);
            $table->string('created_by',20);
            $table->string('updated_by',20);
            $table->timestamps();

            $table->index('username');
            $table->index('sector_n1_id');
            $table->index('time_old');
            $table->index('time_label');
            $table->index('time_start');
            $table->index('time_end');
            $table->index('status');
            $table->index('created_by');
            $table->index('updated_by');
            $table->index('created_at');
            $table->index('updated_at');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // drop table
        Schema::connection('modules')->dropIfExists('trade_times');
        // drop sequence
     
    }
};
