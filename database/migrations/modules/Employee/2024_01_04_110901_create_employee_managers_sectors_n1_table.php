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
        Schema::connection('modules')->create('employee_sectors_n1_managers', function (Blueprint $table) {
                $table->unsignedBigInteger('sector_n1_id');
                $table->string('manager_username', 20);
                $table->bigInteger('hierarchical_level');
                $table->timestamps();
                
                $table->unique([
                    'sector_n1_id',
                    'manager_username',
                    'hierarchical_level',
                ], 'un_employ_sn1_managers');
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('modules')->dropIfExists('employee_sectors_n1_managers');
    }
};
