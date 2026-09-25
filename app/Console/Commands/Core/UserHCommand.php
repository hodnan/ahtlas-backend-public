<?php

namespace App\Console\Commands\Core;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Addons\Gip;
use App\Models\Core\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\LazyCollection;


class UserHCommand extends Command
{

  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'userh:update';

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

    // Define a data inicial
    $startDate = Carbon::parse('2024-06-31'); // Mês atual
    $endDate = Carbon::create(2019, 1, 1)->startOfMonth(); // Janeiro de 2019

    
    while ($startDate->gte($endDate)) {
      $monthStart = $startDate->copy()->startOfMonth()->format('Y-m-d');
      $monthEnd = $startDate->copy()->endOfMonth()->format('Y-m-d');
      
      $hash = Hash::make(Str::uuid());
      $createdAt = Carbon::now();
      
      $users = DB::connection('mis_primary')
      ->table('DB_RH.dbo.VW_QUADRO_DIARIO')
        ->select([
          DB::raw("'usr' + CAST(CD_MATRICULA AS VARCHAR) as username"),
          "NO_NOME as name",
          "NO_SITE_UF as uf",
          'NO_CARGO as position',
          "NO_CARGO_RESUMIDO as position_summary",
          "CD_SETOR as sector_n1_id",
          "CD_SUBSETOR as sector_n2_id",
          DB::raw("'usr' + CAST(CD_MAT_SUPERVISOR AS VARCHAR) as manager_n1_id"),
          DB::raw("'usr' + CAST(CD_MAT_COORDENADOR AS VARCHAR) as manager_n2_id"),
          DB::raw("'usr' + CAST(CD_MAT_GERENTE_LOCAL AS VARCHAR) as manager_n3_id"),
          DB::raw("'usr' + CAST(CD_MAT_GERENTE_RELAC AS VARCHAR) as manager_n4_id"),
          DB::raw("'usr' + CAST(CD_MAT_DIRETOR AS VARCHAR) as manager_n5_id"),
          "NO_STATUS_GIP as status",
          "DT_ADMISSAO as admission",
          DB::raw("CASE WHEN DT_RESCISAO <> '1900-01-01' THEN DT_RESCISAO end as dismissal"),
          "HR_ENTRADA as start_time",
          "HR_JORNADA as working_hours"
          ])
        ->whereBetween('DT_DATA', [$monthStart, $monthEnd])
        ->get()
        ->toArray();
        
        // Converte os usuários para uma LazyCollection e processa em chunks
        LazyCollection::make($users)
        ->chunk(999)
        ->each(function ($chunk) use ($hash, $createdAt,  $startDate) {
         
          // Preparar os dados para inserção
          $updates = $chunk->map(function ($user) use ($hash, $createdAt) {
            $user = (array) $user;
            $user['hierarchical_level'] = 0;
            $user['hierarchical_level'] = $user['username'] == $user['manager_n1_id'] ? 1 : $user['hierarchical_level'];
            $user['hierarchical_level'] = $user['username'] == $user['manager_n2_id'] ? 2 : $user['hierarchical_level'];
            $user['hierarchical_level'] = $user['username'] == $user['manager_n3_id'] ? 3 : $user['hierarchical_level'];
            $user['hierarchical_level'] = $user['username'] == $user['manager_n4_id'] ? 4 : $user['hierarchical_level'];
            $user['hierarchical_level'] = $user['username'] == $user['manager_n5_id'] ? 5 : $user['hierarchical_level'];
            $dateIsVeteran = $value['dt_resc'] ?? Carbon::now();

            return [
              'id' => crc32($user['username']),
              'username' => $user['username'],
              'name' => ucfirstException($user['name']),
              'uf' => $user['uf'],
              'position' => ucfirstException($user['position']),
              'position_summary' => ucfirstException($user['position_summary']),
              'sector_n1_id' => $user['sector_n1_id'],
              'sector_n2_id' => $user['sector_n2_id'],
              'manager_n1_id' => $user['manager_n1_id'],
              'manager_n2_id' => $user['manager_n2_id'],
              'manager_n3_id' => $user['manager_n3_id'],
              'manager_n4_id' => $user['manager_n4_id'],
              'manager_n5_id' => $user['manager_n5_id'],
              'hierarchical_level' => $user['hierarchical_level'],
              'type' => 'usr',
              'staff' => !(preg_match('/Agente|Jovem/i', $user['position_summary']) === 1) || preg_match('/Monitor|Staff/i', $user['position']),
              'admission' => $user['admission'],
              'dismissal' => $user['dismissal'],
              'start_time' => $user['start_time'],
              'working_hours' => $user['working_hours'],
              'is_veteran' => Carbon::parse($user['admission'])->diffInDays($dateIsVeteran) > 90,
              'active' => $user['dismissal'] && str_contains($user['status'], 'RESC') ? 0 : 1,
              'status' => ucfirstException($user['status']),
              'email' => $user['username'] . '@' . config('ahtlas.users.email_domain'),
              'password' => $hash,
              'deleted_at' => $user['dismissal'],
              'created_at' => $createdAt,
              'updated_at' => $createdAt,
            ];
          })->toArray();

          // Usar transações para inserir/atualizar em lote
          foreach ($updates as $user)  {
            $user = (array) $user;
          
            $exists = User::withTrashed()->where('username',$user['username'])->exists();

            if (!$exists) {
             

              try {
                User::create($user);
              } catch (\Throwable $e) {
                Log::error("Erro ao inserir usuário {$user['username']}: " . $e->getMessage());
              }
            }
          }
        });
        Log::info("Finalizou o mês $startDate");

      $startDate->subMonth();
    }
    return Command::SUCCESS;
  }
}
