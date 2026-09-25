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
        Schema::connection('modules')->create('bonus_panel_historicals', function (Blueprint $table) {
            $table->id();
            $table->string('panel_id', 15);
            $table->integer('status')->nullable();;
            $table->json('meta');
            $table->longText('notes')->nullable();
            $table->string('created_by',20)->nullable();;
            $table->string('updated_by',20)->nullable();; 
            $table->timestamps();

            $table->index('panel_id');
            $table->index('status');
            $table->index('created_by');
            $table->index('updated_by');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('modules')->dropIfExists('bonus_panel_historicals');
    }
};
