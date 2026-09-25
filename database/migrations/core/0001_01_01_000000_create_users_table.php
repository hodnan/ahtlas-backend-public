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
        Schema::connection('core')->create('users', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->string('username', 20)->unique();
            $table->string('name');

            // <GIP>
            $table->string('nickname',20)->nullable();
            $table->binary('avatar')->nullable();
            
            $table->string('uf',5);
            $table->string('position', 100);
            $table->string('position_summary', 50);
            $table->bigInteger('sector_n1_id');
            $table->bigInteger('sector_n2_id')->default(0);
            $table->string('manager_n1_id')->nullable();
            $table->string('manager_n2_id')->nullable();
            $table->string('manager_n3_id')->nullable();
            $table->string('manager_n4_id')->nullable();
            $table->string('manager_n5_id')->nullable();

            $table->integer('hierarchical_level');
            $table->string('type', 5)->nullable();
            $table->boolean('staff')->nullable();
            $table->date('admission')->nullable();
            $table->date('dismissal')->nullable();
          
            $table->time('start_time')->nullable();
            $table->time('working_hours')->nullable();
            $table->boolean('is_veteran')->nullable();
            $table->boolean('active');
            $table->string('status');

            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::connection('core')->create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::connection('core')->create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('core')->dropIfExists('users');
        Schema::connection('core')->dropIfExists('password_reset_tokens');
        Schema::connection('core')->dropIfExists('sessions');
    }
};
