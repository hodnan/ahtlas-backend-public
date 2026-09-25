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
        Schema::connection('modules')->create('bonus_indicator_dnas', function (Blueprint $table) {
            $table->id();
            $table->string('public_id', 15)->unique();
            $table->bigInteger('indicator_id');
            $table->unsignedBigInteger('sector_owner_budget');
            $table->unsignedBigInteger('sector_owner_result');
            $table->integer('impact');
            $table->integer('measurement_frequency');
            $table->json('source');
            $table->longText('calc');
            $table->longText('composition');
            $table->longText('notes');
            $table->string('created_by',20);
            $table->string('updated_by',20);
            $table->timestamps();

            $table->index('indicator_id');
            $table->index('sector_owner_budget');
            $table->index('sector_owner_result');
            $table->index('impact');
            $table->index('measurement_frequency');
            $table->index('created_by');
            $table->index('updated_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('modules')->dropIfExists('bonus_indicator_dnas');
    }
};
