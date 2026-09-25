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
        Schema::connection('core')->create('sectors_n1', function (Blueprint $table) {
            $table->BigInteger('id')->primary();
            $table->string('name');
            $table->string('uf', 5);
            $table->integer('hc')->nullable();
            $table->boolean('active')->default(1);
            $table->timestamps();
            $table->softDeletes();

            // índices
            $table->index('name');
            $table->index('uf');
            $table->index('active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('core')->dropIfExists('sectors_n1');
    }
};
