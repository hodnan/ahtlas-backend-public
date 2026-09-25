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
        Schema::connection('modules')->create('control_center_trackings', function (Blueprint $table) {
            $table->id();
            $table->date('month_ref');
            $table->bigInteger('sector_n1_id');
            $table->bigInteger('indicator_id');
            $table->integer('notify_level');
            $table->integer('stage')->nullable()->default(0);
            $table->float('goal');
            $table->float('bypass');
            $table->jsonb('owners')->default(json_encode([]));
            $table->float('q1');
            $table->float('q2');
            $table->float('q3');
            $table->float('q4');
            $table->jsonb('daily_goals');
            $table->boolean('active')->default(1);
            $table->string('created_by', 20)->default('cs000000')->nullable();
            $table->string('updated_by', 20)->default('cs000000')->nullable();
            $table->timestamps();

            $table->unique(['month_ref', 'sector_n1_id', 'indicator_id']);
            $table->index('month_ref');
            $table->index('indicator_id');
            $table->index('sector_n1_id');
            $table->index('notify_level');
            $table->index('active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('modules')->dropIfExists('control_center_trackings');
    }
};
