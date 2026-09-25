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
        Schema::connection('modules')->create('report_favorites', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('report_id');
            $table->boolean('is_favorite')->default(0);
            $table->string('created_by', 20);
            $table->string('updated_by', 20);
            $table->timestamps();

            $table->unique(['report_id', 'updated_by'], 'report_favorites_un');
            $table->index('report_id');
            $table->index('updated_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('modules')->dropIfExists('report_favorites');
    }
};
