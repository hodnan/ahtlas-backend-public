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
        Schema::connection('modules')->create('control_center_tracking_daily_result_sectors', function (Blueprint $table) {
            $table->id();
            $table->date('month_ref');
            $table->date('date_ref');
            $table->bigInteger('sector_n1_id');
            $table->bigInteger('indicator_id');
            $table->float('factor_0')->nullable();
            $table->float('factor_1')->nullable();
            $table->float('result')->nullable();
            $table->float('goal')->nullable();
            $table->float('bypass')->nullable();
            $table->integer('stage')->nullable();
            $table->timestamps();

            $table->unique([
                'month_ref',
                'date_ref',
                'sector_n1_id',
                'indicator_id'
            ]);

            $table->index('month_ref');
            $table->index('date_ref');
            $table->index('indicator_id');
            $table->index('sector_n1_id');         
            $table->index('stage');         
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('modules')->dropIfExists('control_center_tracking_daily_result_sectors');
    }
};
