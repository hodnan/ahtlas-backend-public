<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('modules')->create('control_center_group_sectors', function (Blueprint $table) {
            $table->bigInteger('id')->primary(); 
            $table->string('name'); 
            $table->jsonb('sectors')->default(json_encode([]));
            $table->boolean('active')->default(1);
            $table->string('created_by', 20)->nullable();
            $table->string('updated_by', 20)->nullable();
            $table->timestamps();
        });

        // Cria a sequência que começa em -5000 e decrementa
        DB::connection('modules')->statement('CREATE SEQUENCE control_center_group_id_seq START -5000 INCREMENT -1;');

        // Altera a tabela para usar a sequência para a coluna id
        DB::connection('modules')->statement("ALTER TABLE control_center_group_sectors ALTER COLUMN id SET DEFAULT nextval('control_center_group_id_seq');");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove a tabela
        Schema::connection('modules')->dropIfExists('control_center_group_sectors');

        // Remove a sequência
        DB::connection('modules')->statement('DROP SEQUENCE IF EXISTS control_center_group_id_seq;');
    }
};
