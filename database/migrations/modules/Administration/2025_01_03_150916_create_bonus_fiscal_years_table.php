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
        Schema::connection('modules')->create('bonus_fiscal_years', function (Blueprint $table) {
            $table->id();
            $table->string('public_id', 15)->unique();
            $table->integer('year');
            $table->date('month_start');
            $table->date('month_end');
            $table->boolean('active');
            $table->string('created_by',20);
            $table->string('updated_by',20);
            $table->timestamps();

            $table->primary(['year', 'month_start', 'month_end'], 'un_bonus_fiscal_year');

            $table->index('year');
            $table->index('month_start');
            $table->index('month_end');
            $table->index('active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('modules')->dropIfExists('bonus_fiscal_years');
    }
};
