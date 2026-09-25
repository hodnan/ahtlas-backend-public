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
        Schema::connection('modules')->create('bonus_block_item_targets', function (Blueprint $table) {
            $table->id();
            $table->string('fiscal_year_id',15);
            $table->string('block_id',15);
            $table->integer('item_id');
            $table->date('month_ref');
            $table->bigInteger('indicator_id');
            $table->float('month_target')->nullable();
            $table->float('month_result')->nullable();
            $table->json('month_detour')->nullable();          
            $table->float('accumulated_target')->nullable();
            $table->float('accumulated_result')->nullable();
            $table->json('accumulated_detour')->nullable();
            $table->float('weight')->nullable();
            $table->float('grade')->nullable();
            $table->float('grade_weight')->nullable();
            $table->string('created_by',20)->nullable();
            $table->string('updated_by',20)->nullable();
            $table->timestamps();          
        
            $table->index('fiscal_year_id');
            $table->index('month_ref');
            $table->index('item_id');
            $table->index('indicator_id');
       
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('modules')->dropIfExists('bonus_block_item_targets');
    }
};
