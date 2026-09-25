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
        Schema::connection('core')->create('menus_n2', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->uuid('menu_n1_id')->nullable();
            $table->string('title');
            $table->string('icon');
            $table->string('to')->nullable();
            $table->integer('order')->nullable();
            $table->boolean('active');
            $table->string('created_by', 20)->nullable();
            $table->string('updated_by', 20)->nullable();
            $table->string('deleted_by', 20)->nullable();
            $table->softDeletes();
            $table->timestamps();

            // Índices
            $table->index('menu_n1_id');
            $table->index('title');
            $table->index('icon');
            $table->index('to');
            $table->index('active');

            $table->unique([
                'menu_n1_id',
                'title',
            ], 'un_menu_n1_id_title');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('core')->dropIfExists('menus_n2');
    }
};
