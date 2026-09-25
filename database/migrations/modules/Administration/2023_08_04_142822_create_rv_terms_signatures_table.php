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
        Schema::connection('modules')->create('rv_terms_signatures', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique()->nullable();
            $table->unsignedBigInteger('term_id');
            $table->date('month_ref');
            $table->string('username', 20);
            $table->binary('term_file')->nullable();
            $table->boolean('accept')->nullable();
            $table->json('accept_meta')->nullable();
            $table->string('created_by', 20)->nullable();
            $table->string('updated_by', 20)->nullable();
            $table->timestamps();

            $table->primary(['term_id', 'month_ref', 'username'], 'rv_terms_signatures_uniq');
            $table->index('term_id');
            $table->index('month_ref');
            $table->index('username');
            $table->index('accept');
            $table->index('created_by');
            $table->index('updated_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('modules')->dropIfExists('rv_terms_signatures');
    }
};
