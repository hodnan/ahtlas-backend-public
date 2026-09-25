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
        Schema::connection('modules')->create('planning_file_loads', function (Blueprint $table) {
            $table->id();
            $table->date('month_ref');
            $table->timestamp('start_at')->nullable();
            $table->timestamp('end_at')->nullable();
            $table->integer('type');
            $table->string('file')->nullable();
            $table->jsonb('errors')->nullable();
            $table->integer('status');
            $table->string('created_by',20);
            $table->string('updated_by',20);
            $table->timestamps();

            $table->index('month_ref');
            $table->index('type');
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
        Schema::connection('modules')->dropIfExists('planning_file_loads');
    }
};
