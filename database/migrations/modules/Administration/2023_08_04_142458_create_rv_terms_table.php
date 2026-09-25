<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('modules')->create('rv_terms', function (Blueprint $table) {
            $table->id();
            $table->date('month_ref');
            $table->string('owner',20);
            $table->bigInteger('sector_n1_id');
            $table->bigInteger('sector_n2_id')->default(0);

            $table->string('campaign', 255);
            $table->bigInteger('payment_id');
            $table->integer('position');
            $table->integer('level');
            $table->float('roof',20,8)->nullable();
            $table->boolean('apprentice')->nullable();
            
            $table->float('budgeted',20,8)->nullable();
            $table->float('mock',20,8)->nullable();
            $table->float('delta',20,8)->nullable();

            $table->string('term_name', 255)->unique();
            $table->binary('term')->nullable();
            $table->integer('version')->default(1);
            $table->date('due_date_at')->nullable();
            $table->longText('notes')->nullable();
            
            $table->json('basket');
            $table->json('accelerator');
            $table->json('deflator');
            $table->json('elimination');

            $table->integer('status');
            
            $table->string('approved_by', 20)->nullable();
            $table->datetime('approved_at')->nullable();
            $table->json('approved_meta', 20)->nullable();

            $table->string('created_by', 20);
            $table->json('created_meta')->nullable();
            $table->string('updated_by', 20)->nullable();

            $table->boolean('evaluation')->default(0);
            $table->timestamps();

            $table->index('month_ref');
            $table->index('owner');
            $table->index('sector_n1_id');
            $table->index('sector_n2_id');
            $table->index('campaign');
            $table->index('payment_id');
            $table->index('level');
            $table->index('apprentice');
            $table->index('term_name');
            $table->index('version');
            $table->index('due_date_at');
            $table->index('status');
            $table->index('created_by');
            $table->index('updated_by');
            $table->index('evaluation');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('modules')->dropIfExists('rv_terms');
    }
};
