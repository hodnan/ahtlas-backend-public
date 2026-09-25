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
        Schema::connection('modules')->create('indicators', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->boolean('direction');
            $table->integer('symbol')->default(0);
            $table->integer('calc');
            $table->boolean('is_percent')->default(0);
            $table->boolean('kpi')->default(1)->nullable();
            $table->boolean('rv')->default(1)->nullable();
            $table->boolean('bonus')->default(0)->nullable();
            $table->longText('notes')->nullable();
            $table->boolean('active');
            $table->string('created_by', 20)->nullable();
            $table->string('updated_by', 20)->nullable();
            $table->timestamps();

            $table->index('name');
            $table->index('direction');
            $table->index('symbol');
            $table->index('calc');
            $table->index('is_percent');
            $table->index('active');
            $table->index('created_by');
            $table->index('updated_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('modules')->dropIfExists('indicators');
    }
};
