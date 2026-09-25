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
        Schema::connection('modules')->create('kpi_relateds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('source_id');
            $table->BigInteger('sector_n1_id');
            $table->BigInteger('indicator_id');
            $table->BigInteger('report_id')->nullable();
            $table->string('data_location')->nullable();
            $table->date('last_update')->nullable();
            $table->float('last_result', 20, 8)->nullable();
            $table->BigInteger('delay')->nullable();
            $table->boolean('on_update')->default(0);
            $table->timestamps();

            $table->index('source_id');
            $table->index('sector_n1_id');
            $table->index('indicator_id');
            $table->index('report_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('modules')->dropIfExists('kpi_relateds');
    }
};
