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
        Schema::connection('modules')->create('report_files', function (Blueprint $table) {
            $table->id();
            $table->integer('year_ref');
            $table->date('month_ref');
            $table->uuid('report_uuid');
            $table->string('file');
            $table->string('created_by', 20)->nullable();
            $table->string('updated_by', 20)->nullable();
            $table->timestamps();

            $table->index('year_ref');
            $table->index('month_ref');
            $table->index('report_uuid');
            $table->index('file');

            $table->unique([
                'year_ref',
                'month_ref',
                'report_uuid',
            ], 'report_files_un');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('modules')->dropIfExists('report_files');
    }
};
