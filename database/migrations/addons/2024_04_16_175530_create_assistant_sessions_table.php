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
        Schema::connection('addons')->create('assistant_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('username', 20)->unique();
            $table->text('bearer')->nullable();
            $table->string('session_id', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('addons')->dropIfExists('assistant_sessions');
    }
};
