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
        Schema::connection('modules')->create('kpi_sources', function (Blueprint $table) {
            $table->id();
            $table->string('db', 50);
            $table->string('source')->nullable()->unique();
            $table->string('owner', 20);
            $table->integer('indicator');
            $table->integer('sla');
            $table->unsignedBigInteger('report_id')->nullable();
            $table->string('data_location')->nullable();
            $table->longText('notes')->nullable();
            $table->boolean('active')->default(0);
            $table->string('created_by', 20)->nullable();
            $table->string('updated_by', 20)->nullable();
            $table->timestamps();

            $table->index('db');
            $table->index('source');
            $table->index('owner');
            $table->index('indicator');
            $table->index('sla');
            $table->index('report_id');
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
        Schema::connection('modules')->dropIfExists('kpi_sources');
    }
};
