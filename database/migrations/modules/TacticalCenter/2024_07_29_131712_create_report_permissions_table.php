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
        Schema::connection('core')->create('report_permissions', function (Blueprint $table) {
           
            $table->id();
            $table->string('route');
            $table->jsonb('username')->default(json_encode([]));
            $table->jsonb('sector_n1_id')->default(json_encode([]));
            $table->jsonb('manager_id')->default(json_encode([]));
            $table->jsonb('hierarchical_level')->default(json_encode([]));
            $table->boolean('staff')->nullable();
            $table->jsonb('position_summary')->default(json_encode([]));
            $table->date('expiration_at')->nullable();
            $table->string('created_by', 20)->nullable();
            $table->string('updated_by', 20)->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();

            // Índex
            $table->index('route');         
            $table->index('staff');

            $table->unique([
                'route',
                'username',
                'sector_n1_id',
                'manager_id',
                'hierarchical_level',
                'staff',
                'position_summary',
            ], 'un_reports_id_title');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('core')->dropIfExists('report_permissions');
    }
};
