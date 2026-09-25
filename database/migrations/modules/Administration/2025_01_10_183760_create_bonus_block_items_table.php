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
        Schema::connection('modules')->create('bonus_block_items', function (Blueprint $table) {
            $table->id();
            $table->string('fiscal_year_id',15);
            $table->string('block_id',15);
            $table->bigInteger('indicator_id');
            $table->string('owner_id');
            $table->string('leader_id');
            $table->string('area',100);
            $table->longText('proof')->nullable();
            $table->integer('accumulation_type');
            $table->float('target')->nullable();
            $table->integer('weight')->nullable();
            $table->json('range')->nullable();
          

            $table->timestamps();

            $table->primary(['block_id', 'indicator_id'], 'bonus_block_items_uniq');

            $table->index('block_id');
            $table->index('indicator_id');
            $table->index('owner_id');
            $table->index('leader_id');
            $table->index('area');
            $table->index('accumulation_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('modules')->dropIfExists('bonus_block_items');
    }
};
