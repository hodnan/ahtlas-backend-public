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
        Schema::connection('modules')->create('head_counts', function (Blueprint $table) {
            $table->id();
            $table->date('month_ref');
            $table->date('date_ref');
            $table->bigInteger('sector_n1_id');
            $table->string('manager_n5_id')->nullable();
            $table->string('manager_n4_id')->nullable();
            $table->string('manager_n3_id')->nullable();
            $table->float('hc_dim')->nullable();
            $table->float('vacation_dim')->nullable();
            $table->float('training_dim')->nullable();
            $table->float('training_initial_dim')->nullable();
            $table->float('training_migration_dim')->nullable();
            $table->float('total_dim')->nullable();
            $table->float('hc_real')->nullable();
            $table->float('vacation_real')->nullable();
            $table->float('training_real')->nullable();
            $table->float('training_initial_real')->nullable();
            $table->float('training_migration_real')->nullable();
            $table->float('training_recycling_real')->nullable();
            $table->float('training_return_leave_real')->nullable();
            $table->float('away_real')->nullable();

            $table->float('to_total_real')->nullable();
            $table->float('to_active_real')->nullable();
            $table->float('to_operation_real')->nullable();
            $table->float('to_training_real')->nullable();

            $table->float('total_real')->nullable();
            $table->float('total_active_real')->nullable();
            $table->float('budgeted_total')->nullable();
            $table->float('hd_client_dim')->nullable();
            $table->float('hc_dif')->nullable();
            $table->float('total_dif')->nullable();
            $table->float('vacation_dif')->nullable();
            $table->float('training_dif')->nullable();
            
            $table->float('training_delivery_this_month')->nullable();
            $table->float('training_delivery_next_month')->nullable();
            $table->integer('version');

            $table->timestamps();

            $table->unique(['month_ref', 'date_ref', 'sector_n1_id'], "head_counts_un");

            $table->index('month_ref');
            $table->index('date_ref');
            $table->index('sector_n1_id');
            $table->index('manager_n5_id');
            $table->index('manager_n4_id');
            $table->index('manager_n3_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('modules')->dropIfExists('head_counts');
    }
};
