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
        Schema::connection('addons')->create('service_position_occupations', function (Blueprint $table) {
            $table->id();
            $table->date('month_ref');
            $table->date('date_ref');
            $table->string('username');
            $table->string('hostname');
            $table->boolean('log');
            $table->jsonb('meta');
            $table->timestamps();

            $table->index('month_ref');
            $table->index('date_ref');
            $table->index('username');
            $table->index('hostname');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('addons')->dropIfExists('service_position_occupations');
    }
};
