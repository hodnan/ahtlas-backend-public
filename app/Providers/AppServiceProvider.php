<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Pulse\Facades\Pulse;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Migrations\Migrator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom($this->getAllMigrationPaths());

        Pulse::user(fn ($user) => [
            'name' => $user->name,
            'extra' => $user->email,
            'avatar' => 'data:image/jpeg;base64,' . $user->avatar->avatar,
        ]);
    }

    protected function getAllMigrationPaths()
    {
        $paths = [
            database_path('migrations/addons'),
            database_path('migrations/core'),
        ];

        // Adiciona dinamicamente todas as subpastas de 'modules'
        $modulesPath = database_path('migrations/modules');
        if (is_dir($modulesPath)) {
            $subfolders = File::directories($modulesPath);
            foreach ($subfolders as $folder) {
                // Obtém o caminho relativo a partir do diretório base
                $relativePath = 'database/migrations/modules/' . basename($folder);
                $paths[] = $relativePath;
            }
        }

        return $paths;
    }
}
