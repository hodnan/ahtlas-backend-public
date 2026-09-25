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
        Schema::connection('core')->create('notifications', function (Blueprint $table) {
            $table->id();
            $table->integer('type');
            $table->string('title', 50);
            $table->longText('notes')->nullable();
            $table->string('route')->nullable();
            $table->string('external_link')->nullable();
            $table->binary('img')->nullable();
            $table->json('username')->nullable();
            $table->json('sector_n1_id')->nullable();
            $table->json('managers')->nullable();
            $table->json('hierarchical_level')->nullable();
            $table->boolean('staff')->nullable();
            $table->json('uf')->nullable();
            $table->json('position_summary')->nullable();
            $table->integer('delivered')->nullable();
            $table->integer('received')->nullable();
            $table->integer('read')->nullable();
            $table->date('start')->nullable();
            $table->date('end')->nullable();
            $table->integer('status')->default(0);
            $table->string('created_by', 20)->nullable();
            $table->string('updated_by', 20)->nullable();
            $table->timestamps();

            $table->index('type');
            $table->index('title');
            $table->index('route');
            $table->index('external_link');
            $table->index('start');
            $table->index('end');
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
        Schema::connection('core')->dropIfExists('notifications');
    }
};
