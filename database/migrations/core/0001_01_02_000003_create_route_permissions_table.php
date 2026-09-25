<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::connection('core')->create('route_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('route');
            $table->boolean('is_display');
            $table->jsonb('username')->default(json_encode([]));
            $table->jsonb('sector_n1_id')->default(json_encode([]));
            $table->jsonb('manager_n1_id')->default(json_encode([]));
            $table->jsonb('manager_n2_id')->default(json_encode([]));
            $table->jsonb('manager_n3_id')->default(json_encode([]));
            $table->jsonb('manager_n4_id')->default(json_encode([]));
            $table->jsonb('manager_n5_id')->default(json_encode([]));
            $table->jsonb('hierarchical_level')->default(json_encode([]));
            $table->string('staff', 20)->nullable();
            $table->jsonb('type')->default(json_encode([]));
            $table->jsonb('uf')->default(json_encode([]));
            $table->jsonb('position_summary')->default(json_encode([]));
            $table->date('expiration_at')->nullable();
            $table->string('created_by', 20)->nullable();
            $table->string('updated_by', 20)->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();

            // Índex
            $table->index('username');
            $table->index('sector_n1_id');
            $table->index('manager_n1_id');
            $table->index('manager_n2_id');
            $table->index('manager_n3_id');
            $table->index('manager_n4_id');
            $table->index('manager_n5_id');
            $table->index('hierarchical_level');
            $table->index('staff');
            $table->index('type');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('core')->dropIfExists('route_permissions');
    }
};
