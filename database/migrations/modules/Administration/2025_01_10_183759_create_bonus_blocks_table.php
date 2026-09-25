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
        Schema::connection('modules')->create('bonus_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('public_id', 15)->unique();
            $table->string('name', 50);
            $table->string('fiscal_year_id', 15);
            $table->string('owner_id', 20)->nullable();
            $table->boolean('default')->default(0);
            $table->float('weight')->nullable();
            $table->float('order')->nullable();
            $table->float('grade')->nullable();
            $table->float('grade_weight')->nullable();
            $table->string('created_by',20);
            $table->string('updated_by',20);
            $table->timestamps();
            
            $table->index('public_id');
            $table->index('fiscal_year_id');
            $table->index('name');
            $table->index('owner_id');
            $table->index('order');
            $table->index('default');
            $table->index('created_by');
            $table->index('updated_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('modules')->dropIfExists('bonus_blocks');
    }
};
