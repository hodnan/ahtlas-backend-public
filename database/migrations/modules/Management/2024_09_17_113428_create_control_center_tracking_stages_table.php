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
        Schema::connection('modules')->create('control_center_tracking_stages', function (Blueprint $table) {
            $table->id();
            $table->date('month_ref');
            $table->date('date_ref');
            $table->bigInteger('sector_n1_id');
            $table->bigInteger('indicator_id');
            $table->float('factor_0');
            $table->float('factor_1');
            $table->float('result');
            $table->float('goal');
            $table->float('bypass');
            $table->boolean('validated')->default(0);
            $table->integer('actions')->default(0);
            $table->longText('notes')->nullable();
            $table->json('steps')->nullable();
            $table->integer('stage')->default(0);
            $table->integer('status')->default(0);
            $table->string('created_by', 20)->default('cs000000')->nullable();
            $table->string('updated_by', 20)->default('cs000000')->nullable();
            $table->timestamps();

 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('modules')->dropIfExists('control_center_tracking_stages');
    }
};
