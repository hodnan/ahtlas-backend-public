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
        Schema::connection('modules')->create('rv_terms_panel_signatures', function (Blueprint $table) {
            $table->id();
            $table->date('month_ref');
            $table->string('term_file')->nullable();
            $table->string('username', 20)->nullable();
            $table->string('uuid', 40)->nullable();
            $table->string('hostname', 40)->nullable();
            $table->string('ip', 20)->nullable();
            $table->dateTime('available_at')->nullable();
            $table->dateTime('accepted_at')->nullable();            
            $table->boolean('accept')->nullable();
            $table->timestamps();

            $table->index('month_ref');
            $table->index('term_file');
            $table->index('username');
            $table->index('uuid');
            $table->index('hostname');
            $table->index('ip');
            $table->index('available_at');
            $table->index('accepted_at');
            $table->index('accept');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('modules')->dropIfExists('rv_terms_panel_signatures');
    }
};
