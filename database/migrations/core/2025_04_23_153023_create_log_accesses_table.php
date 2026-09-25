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
        Schema::connection('core')->create('log_accesses', function (Blueprint $table) {
            $table->id();
            $table->date('month_ref');
            $table->date('date_ref');            
            $table->string('username', 20);
            $table->string('method', 10);
            $table->string('route');
            $table->json('route_parameters')->nullable();
            $table->json('meta')->nullable();
            $table->boolean('authorized')->nullable();
            $table->timestamps();

            $table->index('month_ref');
            $table->index('date_ref');
            $table->index('username');
            $table->index('method');
            $table->index('route');
            $table->index('authorized');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('core')->dropIfExists('log_accesses');
    }
};
