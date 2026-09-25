<?php 
namespace App\Providers;

use Illuminate\Database\Console\Migrations\MigrateCommand;
use Illuminate\Support\Facades\File;

class MigrationServiceProvider extends MigrateCommand
{
    public function paths()
    {
        $migrationPaths = [
            database_path('migrations'),
        ];

        // Adiciona dinamicamente todas as subpastas
        $subfolders = File::allDirectories(database_path('migrations'));
        $migrationPaths = array_merge($migrationPaths, $subfolders);

        return $migrationPaths;
    }
}
