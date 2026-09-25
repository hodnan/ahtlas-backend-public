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
        Schema::connection('addons')->create('calendars', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->date('year_ref');
            $table->date('month_ref');
            $table->integer('year');
            $table->integer('month');
            $table->integer('day');
            $table->integer('weekday');
            $table->boolean('business_day');
            $table->jsonb('holiday_uf')->default(json_encode([]));
            $table->boolean('holiday_br')->nullable();
            $table->boolean('active');
            $table->boolean('passed');
            $table->timestamps();

            // Índex
            $table->index('year');
            $table->index('month');
            $table->index('day');
            $table->index('date');
            $table->index('weekday');
            $table->index('business_day');
            $table->index('active');
            $table->index('passed');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('addons')->dropIfExists('calendars');
    }
};
