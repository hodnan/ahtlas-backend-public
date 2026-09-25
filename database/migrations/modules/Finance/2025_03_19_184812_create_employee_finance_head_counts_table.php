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
        Schema::connection('modules')->create('employee_finance_head_counts', function (Blueprint $table) {
            $table->id();
            $table->string('position_summary', '50')->nullable();
            $table->string('position', '150')->nullable();
            $table->integer('sector_n1_id')->nullable();
            $table->time('working_hours')->nullable();
            $table->string('fat', '50')->nullable();
            $table->string('position_lpu', '150')->nullable();
            $table->string('group_lpu', '150')->nullable();
            $table->float('price_lpu')->nullable();
            $table->boolean('active')->nullable();
            $table->timestamps();

            $table->index('position_summary');
            $table->index('position');
            $table->index('sector_n1_id');
            $table->index('working_hours');          
            $table->index('position_lpu');
            $table->index('group_lpu');          
            $table->index('active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('modules')->dropIfExists('employee_finance_head_counts');
    }
};
