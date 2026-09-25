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
        Schema::connection('modules')->create('report_reads', function (Blueprint $table) {
            $table->id();
            $table->date('month_ref');
            $table->date('date_ref');
            $table->unsignedBigInteger('report_id');
            $table->string('username');
            $table->string('file_name');
            $table->jsonb('meta');
            $table->jsonb('errors')->nullable();
            $table->timestamps();

            $table->index('month_ref');
            $table->index('date_ref');
            $table->index('report_id');
            $table->index('username');
            $table->index('file_name');
       
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('modules')->dropIfExists('report_reads');
    }
};
