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
        Schema::connection('core')->table('users', function (Blueprint $table) {
          
            $table->date('birthdate')->nullable()->after('dismissal');
         
            $table->index('name');
            $table->index('uf');
            $table->index('position');
            $table->index('position_summary');
            $table->index('sector_n1_id');
            $table->index('sector_n2_id');
            $table->index('manager_n1_id');
            $table->index('manager_n2_id');
            $table->index('manager_n3_id');
            $table->index('manager_n4_id');
            $table->index('manager_n5_id');
            $table->index('admission');
            $table->index('dismissal');
            $table->index('birthdate');
            $table->index('hierarchical_level');
            $table->index('type');
            $table->index('active');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::connection('core')->table('users', function (Blueprint $table) {
                 
            $table->dropIndex('users_name_index');
            $table->dropIndex('users_uf_index');
            $table->dropIndex('users_position_index');
            $table->dropIndex('users_position_summary_index');
            $table->dropIndex('users_sector_n1_id_index');
            $table->dropIndex('users_sector_n2_id_index');
            $table->dropIndex('users_manager_n1_id_index');
            $table->dropIndex('users_manager_n2_id_index');
            $table->dropIndex('users_manager_n3_id_index');
            $table->dropIndex('users_manager_n4_id_index');
            $table->dropIndex('users_manager_n5_id_index');
            $table->dropIndex('users_admission_index');
            $table->dropIndex('users_dismissal_index');
            $table->dropIndex('users_birthdate_index');
            $table->dropIndex('users_hierarchical_level_index');
            $table->dropIndex('users_type_index');
            $table->dropIndex('users_active_index');
            $table->dropIndex('users_status_index');

            // Remove a coluna
            $table->dropColumn('birthdate');
        });
    }
};
