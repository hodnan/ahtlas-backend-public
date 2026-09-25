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
        Schema::connection('core')->create('menus_n1', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->uuid('module_id')->nullable();;
            $table->string('title');
            $table->string('icon');
            $table->integer('order')->nullable();
            $table->boolean('active');
            $table->string('created_by', 20)->nullable();
            $table->string('updated_by', 20)->nullable();
            $table->string('deleted_by', 20)->nullable();
            $table->softDeletes();
            $table->timestamps();

            // Índices

            $table->index('module_id');
            $table->index('title');
            $table->index('icon');
            $table->index('active');

            $table->unique([
                'module_id',
                'title',
            ], 'un_menu_id_title');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('core')->dropIfExists('menus_n1');
    }
};
