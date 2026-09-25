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
        Schema::connection('core')->create('notification_users', function (Blueprint $table) {
            $table->id();
            $table->string('username',20);
            $table->unsignedBigInteger('notification_id');
            $table->boolean('delivered')->default(0);;
            $table->boolean('received')->default(0);;
            $table->boolean('read')->default(0);
            $table->date('start')->nullable();
            $table->date('end')->nullable();
            $table->json('meta')->nullable();
            $table->string('created_by', 20)->nullable();
            $table->string('updated_by', 20)->nullable();
            $table->timestamps();

            $table->index('username');
            $table->index('notification_id');
            $table->index('delivered');
            $table->index('received');
            $table->index('read');
            $table->index('start');
            $table->index('end');
            $table->index('created_by');
            $table->index('updated_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('core')->dropIfExists('notification_users');
    }
};
