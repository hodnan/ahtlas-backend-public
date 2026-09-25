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
        Schema::connection('modules')->create('bonus_panel_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('panel_id', 15);            
            $table->string('block_id', 15);            
            $table->string('fiscal_year_id', 15);
            $table->integer('order');
            $table->integer('weight');
            $table->float('grade')->nullable();
            $table->float('grade_weight')->nullable();
            $table->string('created_by',20);
            $table->string('updated_by',20);
            $table->timestamps();

            $table->index('panel_id');
            $table->index('block_id');
            $table->index('fiscal_year_id');
            $table->index('created_by');
            $table->index('updated_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('modules')->dropIfExists('bonus_panel_blocks');
    }
};
