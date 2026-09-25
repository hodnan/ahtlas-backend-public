<?php

namespace App\Console\Commands\Core;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Addons\Gip;
use App\Models\Core\User;
use App\Services\Core\User\UserService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\LazyCollection;

class UsersAvatarCommand extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'users:avatar';

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
    // $users = User::whereNull('avatar')->orWhereNull('nickname')->get(['username']);
    $users =  User::doesntHave('avatar')->get(['username']);

    foreach ($users as $key => $value) {
      try {
        UserService::setAvatarNickname($value['username']);
      } catch (\Throwable $e) {
        Log::error($e->getMessage());
      }
    }

    return Command::SUCCESS;
  }
}
