<?php

namespace App\Console\Commands\Modules\Employee;

use Illuminate\Console\Command;
use App\Models\Core\User;
use App\Models\Modules\Employee\Employee;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\AssignOp\Coalesce;

class EmployeeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employee:data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = DB::connection('core')
        ->table('users')
        ->select([
            'username',
            'name',
            'uf',
            'position',
            'position_summary',
            'sector_n1_id',
            'sector_n2_id',
            'manager_n1_id',
            'manager_n2_id',
            'manager_n3_id',
            'manager_n4_id',
            'manager_n5_id',
            'hierarchical_level',
            'type',
            'staff',
            'admission',
            'dismissal',
            'birthdate',
            'is_veteran',
            'start_time',
            'working_hours',
            'active',
            'status'
        ])
        ->get()
        ->toArray();
       
        Employee::truncate();

        LazyCollection::make($users)
            ->chunk(999)
            ->each(function ($chunk) {
                try {
                    // Converta cada stdClass para array associativo
                    $data = $chunk->map(function ($user) {
                        return (array) $user;
                    })->toArray();
    
                    Employee::insert($data);
                } catch (\Throwable $e) {
                    Log::error($e->getMessage());
                }
            });

        return Command::SUCCESS;
    }
}
