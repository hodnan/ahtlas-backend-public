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
        Schema::connection('modules')->create('bonus_panels', function (Blueprint $table) {
            $table->id();
            $table->string('public_id', 15)->unique();
            $table->string('fiscal_year_id', 15);
            $table->string('owner_id', 20);
            $table->integer('hierarchical_level');
            $table->string('area',100);            
            $table->integer('status')->default(0);   
            $table->string('created_by',20);
            $table->string('updated_by',20);         
            $table->timestamps();

            $table->primary(['fiscal_year_id', 'owner_id', 'area', 'hierarchical_level'], 'bonus_panels_uniq');

            $table->index('public_id');
            $table->index('fiscal_year_id');
            $table->index('owner_id');
            $table->index('hierarchical_level');
            $table->index('area');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('modules')->dropIfExists('bonus_panels');
    }
};
