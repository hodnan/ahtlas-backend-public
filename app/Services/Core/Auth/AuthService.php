<?php

namespace App\Services\Core\Auth;

use App\Models\Core\Log\LogAuth;
use App\Models\Core\Module;
use App\Models\Core\RoutePermission;
use App\Models\Core\User;

use App\Services\Core\Auth\AuthInterface;
use App\Services\Core\User\MetadataService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AuthService
{
    use ApiResponser;

    public static function login($request)
    {
        $username = str_replace("ext", "usr", strtolower($request->username));
        $password = $request->password;
        $remember = $request->remember;
        $auth = [
            'authorize' => false,
            'local' => false,
            'error' => 'Erro não definido',
            'log_notes' => null,
        ];

        $log = new LogAuth();
        $log->month_ref = Carbon::now()->startOfMonth();
        $log->date_ref = Carbon::now()->startOfDay();
        $log->username = $username;
        $log->meta = MetadataService::getMetadata($request, $username);
        $log->authorized = 0;
        $log->save();

        $type = strtolower(preg_replace('/[0-9]/', '', $request->username));
        $user =  User::where('username', $username)->where('active', 1)->get(['id'])->first();

        // Autenticação direta no ahtlas
        $auth = self::authLocal($username, $password, $remember);

        // Autenticações externas
        if (!$auth['authorize']) {

            $provider =  AuthInterface::TYPES[$type]['provider'] ?? null;

            switch ($provider) {
                case 'corporate':
                    $auth = self::authCorporateIdp($username, $password);
                    break;
                case 'ldap':
                    $auth = self::authLdap($username, $password, $user);
                    break;
                default:
                    break;
            }
        }

        if ($auth['authorize'] & !$auth['local']) {
            $auth['authorize'] = Auth::loginUsingId($user->id, $remember);
        }

        $log->notes = $auth['log_notes'];

        //  Usuário autenticado, finaliza outras sessões e retorna sucesso
        if ($auth['authorize']) {

            $currentSessionId = session()->getId(); // Obtém o ID da sessão atual
            DB::table('sessions')
                ->where('user_id', $user->id)
                ->where('id', '!=', $currentSessionId) // Exclui todas, exceto a sessão atual
                ->delete();

            $user->tokens()->where('name', 'Session')->delete();
            $token = $user->createToken('Session')->plainTextToken;

            // Atualiza o log como autorizado
            $log->authorized = 1;
            $log->save();

            return ApiResponser::success('Usuário autenticado com sucesso', [$token]);
        }

        // Atualiza o log como não autorizado
        $log->authorized = 0;
        $log->save();

        return ApiResponser::error('Falha ao autenticar', [
            [$auth['error']],
            ['Use sua matrícula e a senha do IdP corporativo'],
            ['aguarde um minuto e tente novamente'],
        ]);
    }

    public static function logout($request): void
    {

        try {
            // Logout do usuário
            Auth::guard('web')->logout();

            // Invalidar a sessão
            $request->session()->invalidate();

            // Regenerar o token CSRF
            $request->session()->regenerateToken();

            // Remover cookies de sessão
            Cookie::queue(Cookie::forget('XSRF-TOKEN'));
            Cookie::queue(Cookie::forget('laravel_session'));

            // Remover todos os tokens do usuário (caso esteja usando Laravel Sanctum)
            $user = $request->user();
            if ($user) {
                $user->tokens()->delete();
                $userId = $user->id;
            } else {
                $userId = null;
            }

            // Remover a sessão do banco de dados
            DB::table('sessions')->where('user_id', $userId)->delete();
        } catch (\Throwable $e) {
            Log::info('logout', [$e->getMessage()]);
            throw new \Exception($e->getMessage());
        }
    }

    public static function authCorporateIdp($username, $password)
    {
        $auth = [
            'authorize' => false,
            'local' => false,
            'error' => 'Falha ao autenticar (IdP corporativo)',
            'log_notes' => 'Falha ao autenticar (IdP corporativo)',
        ];

        try {
            $data = Http::withOptions([
                'verify' => false,
                'curl' => [
                    CURLOPT_SSL_CIPHER_LIST => 'DEFAULT@SECLEVEL=1',
                ],
            ])->withHeaders([
                'Accept' => 'application/json',
            ])->post(
                config('services.corporate_idp.url'),
                ['matricula' => $username, 'senha' => $password]
            );

            $response = json_decode($data->body());

            if (isset($response->Autenticado)) {
                $auth['authorize'] = true;
                $auth['log_notes'] = 'Usuário autenticado (IdP corporativo)';
            };

            if (!$response->Autenticado || $response->message) {
                $auth['authorize'] = false;
                $auth['error'] = $data['Erro'] ??  $response->message;
                $auth['log_notes'] =  $data['Erro'] ?? $response->message;
            }
        } catch (\Throwable $e) {
            // dd('aqui');
            // Log::info('authCorporateIdp', [$e->getMessage()]);
            $auth['error'] =  'Falha ao autenticar (IdP corporativo)';
            $auth['log_notes'] = 'Falha ao autenticar (IdP corporativo)';
        }

        return $auth;
    }

    public static function authLdap($username, $password, $user): array
    {
        $auth = [
            'authorize' => false,
            'local' => false,
            'error' => 'Erro não definido',
        ];

        $ldapExist = false;

        // Se não existir o usuário no banco, verifica no diretório LDAP
        if (!$user) {
            $userLdap = self::userExistLdapDirectory($username);
            $ldapExist = $userLdap['exist'];
        }

        // Não existindo no banco nem no diretório LDAP, finaliza com erro
        if (!$ldapExist && !$user) {
            $auth['log_notes'] = `Usuário não localizado ('$username')`;
            $auth['error'] = `Usuário não localizado ('$username')`;
        }

        // Tenta logar no diretório LDAP
        try {
            $ldap = self::authLdapDirectory($username, $password);

            // cadastra novo usuário no banco se não existir 
            if ($ldap['auth'] && !$user) {

                // return [$ldap, $user, $username, $password];
                $user = self::createLdapUser($username, $ldap['name']);
                $auth['log_notes'] = $user ? 'Usuário criado (LDAP) | ' : null;
            }

            if ($ldap['auth']) {
                $auth['log_notes'] = 'Autenticado (LDAP)';
                $auth['authorize'] = true;
                $auth['user'] =  $user;
            }

            if ($ldap['error']) {
                $auth['error'] = $ldap['error'];
                $auth['log_notes'] =   $ldap['error'] . " | " . $ldap['body'];
            }
        } catch (\Throwable $e) {
            Log::info('authLdap', [$e->getMessage()]);
            $auth['log_notes'] =  'Falha ao autenticar (LDAP) ';
        }

        return $auth;
    }

    public static function authLocal(string $username, string $password, $remember = false): array
    {
        $auth = [
            'authorize' => false,
            'local' => false,
            'error' => 'Erro não definido',
            'log_notes' => null,
        ];

        try {
            $auth['authorize'] = Auth::attempt(['username' => $username, 'password' => $password], $remember);
            $auth['log_notes'] = $auth['authorize'] ? 'Autenticado local' : 'Falha ao autenticar (local)';
            $auth['local'] = true;
        } catch (\Throwable $e) {
            Log::info('authLocal', [$e->getMessage()]);
            $auth['authorize'] = false;
            $auth['log_notes'] = 'Falha ao autenticar (local)';
        }

        return $auth;
    }

    public static function userExistLdapDirectory(string $username): array
    {
        $username =  str_replace('usr', 'ext', $username);

        Log::info('userExistLdapDirectory');
        try {
            $data = Http::get(config('services.ldap_directory.url'), [
                'Usuario' => $username,
                'Senha' => '',
                'Autenticar' => '',
                'Servidor' => '',
                'Porta' => '',
                'DN' => '',
                'O' => '',
                'AcessoSeguro' => '',
                'Propriedades' => '',
            ]);

            $exist = self::validateAuthInBodyText($data->body());

            return [
                'exist' => $exist,
                'name' => self::nameAuthInBodyText($data->body()),
                'error' => self::errorAuthInBodyText($data->body())
            ];
        } catch (\Throwable $e) {
            Log::info('userExistLdapDirectory', [$e->getMessage()]);
            return ['exist' => false, 'error' =>  'userExistLdapDirectory'];
        }
    }

    public static function authLdapDirectory(string $username, string $password): array
    {
        $username =  str_replace('usr', 'ext', $username);

        try {
            $data = Http::get(config('services.ldap_directory.url'), [
                'Usuario' => $username,
                'Senha' => $password,
                'Autenticar' => '',
                'Servidor' => '',
                'Porta' => '',
                'DN' => '',
                'O' => '',
                'AcessoSeguro' => '',
                'Propriedades' => '',
            ]);

            $auth = self::validateAuthInBodyText($data->body());

            return [
                'auth' => $auth,
                'name' => self::nameAuthInBodyText($data->body()),
                'error' => self::errorAuthInBodyText($data->body()),
                'body' => self::rawBodyText($data->body()),
            ];
        } catch (\Throwable $e) {
            Log::info('authLdapDirectory', [$e->getMessage()]);
            return ['auth' => false, 'error' =>  'Falha ao autenticar (LDAP)'];
        }
    }

    public static function userCheck(string $username): bool
    {
        $user =  User::where('username', $username)->first();
        return !!$user;
    }

    // Verifica se a conexão com o diretório LDAP é válida
    public static function validateAuthInBodyText(string $body): bool
    {
        return str_contains($body, 'Usuario OK');
    }

    // Verifica se houve erro de conexão
    public static function errorAuthInBodyText(string $body)
    {
        $return = null;

        if (str_contains($body, 'device attached')) {
            $return = 'Usuário ou senha inválidos (LDAP)';
        }

        if (str_contains($body, 'Logon failure')) {
            $return = 'Usuário ou senha inválidos (LDAP)';
        }

        if (str_contains($body, 'server is unwilling')) {
            $return = 'Usuário bloqueado, libere no app de OTP (LDAP)';
        }

        return  $return;
    }

    public static function nameAuthInBodyText(string $body): string
    {
        // Expressão regular para capturar o conteúdo entre sn# e #
        $padrao = '/sn#(.*?)#/';

        // Executa a expressão regular
        if (preg_match($padrao, $body, $matches)) {
            // Remove caracteres especiais, exceto espaços
            $name = preg_replace('/[^a-zA-Z0-9|\s]/', '', $matches[1]);

            // Remove | do texto
            $name = preg_replace('/\|/', ' ', $name);
            // Corrige espaçamentos duplos
            $name = preg_replace('/\s+/', ' ', $name);
        } else {
            $name = "";
        }
        return $name;
    }

    public static function rawBodyText(string $body): string
    {
        // Expressão regular para capturar o conteúdo entre "> e </string>
        $padrao = '/">(.*?)<\/string>/s';

        // Executa a expressão regular
        if (preg_match($padrao, $body, $matches)) {
            // Retorna o conteúdo capturado sem alterações
            return $matches[1];
        }

        // Retorna vazio caso nada seja encontrado
        return "";
    }

    public static function allowableRoutes(User $user)
    {
        // Lista as rotas permitidas para o usuário
        $routes = RoutePermission::where(function ($query) use ($user) {

            $query->where(function ($subQuery) use ($user) {
                $subQuery->whereRaw("jsonb_array_length(username) = 0")
                    ->orWhereJsonContains('username', $user->username);
            });
            $query->where(function ($subQuery) use ($user) {
                $subQuery->whereRaw("jsonb_array_length(sector_n1_id) = 0")
                    ->orWhereJsonContains('sector_n1_id', $user->sector_n1_id);
            });

            $query->where(function ($subQuery) use ($user) {
                $subQuery->whereRaw("jsonb_array_length(manager_n1_id) = 0")
                    ->orWhereJsonContains('manager_n1_id', $user->manager_n1_id);
            });
            $query->where(function ($subQuery) use ($user) {
                $subQuery->whereRaw("jsonb_array_length(manager_n2_id) = 0")
                    ->orWhereJsonContains('manager_n2_id', $user->manager_n2_id);
            });
            $query->where(function ($subQuery) use ($user) {
                $subQuery->whereRaw("jsonb_array_length(manager_n3_id) = 0")
                    ->orWhereJsonContains('manager_n3_id', $user->manager_n3_id);
            });

            $query->where(function ($subQuery) use ($user) {
                $subQuery->whereRaw("jsonb_array_length(manager_n4_id) = 0")
                    ->orWhereJsonContains('manager_n4_id', $user->manager_n4_id);
            });
            $query->where(function ($subQuery) use ($user) {
                $subQuery->whereRaw("jsonb_array_length(manager_n5_id) = 0")
                    ->orWhereJsonContains('manager_n5_id', $user->manager_n5_id);
            });

            $query->where(function ($subQuery) use ($user) {
                $subQuery->whereRaw("jsonb_array_length(hierarchical_level) = 0")
                    ->orWhereJsonContains('hierarchical_level', $user->hierarchical_level);
            });
            $query->where(function ($subQuery) use ($user) {
                $subQuery->whereRaw("jsonb_array_length(position_summary) = 0")
                    ->orWhereJsonContains('position_summary', $user->position_summary);
            });
            $query->where(function ($subQuery) use ($user) {
                $subQuery->whereNull("staff")
                    ->orWhere('staff', $user->staff);
            });
            $query->where(function ($subQuery) use ($user) {
                $subQuery->whereRaw("jsonb_array_length(type) = 0")
                    ->orWhereJsonContains('type', $user->type);
            });
        })->pluck('route')->toArray();

        return $routes;
    }

    public static function navigation(User $user)
    {
        $routes = $user->isAdmin() ? [] : self::allowableRoutes($user);

        $routes = array_map(function ($item) {
            return str_replace('/api', '', $item);
        }, $routes);

        $navigation = Module::with(
            ['menuN1' => function ($query) use ($routes) {
                $query->where('active', 1);
                $query->whereHas('menuN2', function ($subQuery) use ($routes) {
                    $subQuery->whereNotNull('id');
                    if (!empty($routes)) {
                        $subQuery->whereIn('to', $routes);
                    }
                });
                $query->orderBy('order', 'asc');
            }, 'menuN1.menuN2' => function ($query) use ($routes) {
                $query->where('active', 1);
                $query->whereNotNull('id');
                if (!empty($routes)) {
                    $query->whereIn('to', $routes);
                }
                $query->orderBy('order', 'asc');
            }]
        )
            ->where('active', 1)
            ->whereHas('menuN1.menuN2')
            ->orderBy('order', 'asc')
            ->get();

        $navigation = $navigation->filter(function ($module) {
            return $module->menuN1->isNotEmpty();
        })->values()->toArray();


        return $navigation;
    }

    public static function createLdapUser($username, $name)
    {
        $type = preg_replace('/[0-9]/', '', $username);
        $password =  env('APP_ENV', 'production') == 'dev' ? Hash::make('123456') : Hash::make(Str::uuid());

        $user = [
            'id' => crc32($username),
            'username' => $username,
            'name' =>  ucfirstException($name),
            'position' => "Usuário externo ($type)",
            'position_summary' => "Usuário externo ($type)",
            'uf' => strtoupper($type),
            'email' => "$username@" . config('ahtlas.users.email_domain'),
            'type' => $type,
            'hierarchical_level' => 0,
            'sector_n1_id' => 0,
            'sector_n2_id' => 0,
            'staff' => 1,
            'password' => $password,
            'status' => "Usuário externo ($type)",
            'active' => 1,
        ];

        return User::create($user);
    }

    public static function getServer($request): string
    {
        $host = $request->getHost() != 'localhost' ?  php_uname('n') : $request->getHost();

        return config('ahtlas.environments')[$host] ?? 'Não localizado';
    }
}
