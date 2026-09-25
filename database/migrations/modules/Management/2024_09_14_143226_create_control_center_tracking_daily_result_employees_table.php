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
        Schema::connection('modules')->create('control_center_tracking_daily_result_employees', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->date('month_ref');
            $table->date('date_ref');
            $table->bigInteger('sector_n1_id');
            $table->bigInteger('indicator_id');
            $table->bigInteger('stage_id');
            $table->string('username', 20);
            $table->float('factor_0');
            $table->float('factor_1');
            $table->float('result');
            $table->float('goal');
            $table->float('bypass');
            $table->float('delta');
            $table->integer('stage');
            $table->integer('quadrant');
            $table->timestamps();

            $table->index('month_ref');
            $table->index('date_ref');
            $table->index('sector_n1_id');
            $table->index('indicator_id');
            $table->index('username');
            $table->index('stage');

            $table->unique([
                'month_ref',
                'date_ref',
                'sector_n1_id',
                'indicator_id',
                'username'
            ], 'control_center_tracking_daily_result_employees_un');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('modules')->dropIfExists('control_center_tracking_daily_result_employees');
    }
};
