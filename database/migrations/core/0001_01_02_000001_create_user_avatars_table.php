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
        Schema::connection('core')->create('user_avatars', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->string('username', 20)->unique();
            $table->string('nickname', 20)->nullable();
            $table->binary('avatar')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('core')->dropIfExists('user_avatars');
    }
};
