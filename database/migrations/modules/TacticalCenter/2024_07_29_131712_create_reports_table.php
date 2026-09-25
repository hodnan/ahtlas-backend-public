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
        Schema::connection('modules')->create('reports', function (Blueprint $table) {
           
            $table->id();
            $table->uuid()->nullable();
            $table->string('title', 164);
            $table->integer('type');
            $table->string('report')->nullable();
            $table->integer('group')->nullable();
            $table->time('schedule_time')->nullable();
            $table->time('atd')->nullable(); //avarege time developer - tempo médio de desenvolvimento
            $table->longText('notes')->nullable();
            $table->string('interval_type')->nullable();
            $table->json('interval_values')->nullable();;
            $table->boolean('active')->default(1);
            $table->string('created_by', 20)->nullable();
            $table->string('updated_by', 20)->nullable();
            $table->timestamps();

            $table->index('title');
            $table->index('type');
            $table->index('group');
            $table->index('active');
            $table->index('created_by');
            $table->index('updated_by');

            $table->unique([
                'title',
                'type',
            ], 'un_reports_id_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('modules')->dropIfExists('reports');
    }
};
