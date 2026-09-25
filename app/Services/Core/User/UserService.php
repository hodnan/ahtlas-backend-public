<?php

namespace App\Services\Core\User;

use App\Models\Addons\User\UserExternal;
use App\Models\Core\User;
use App\Models\Core\UserAvatar;
use Carbon\Carbon;
use Imagine\Image\Box;
use Imagine\Gd\Imagine;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class UserService
{
    public static function getAvatar($username = null)
    {
        $username = $username ?? Auth::user()->username;

        try {
            $response = Http::timeout(30)
                ->get(config('services.avatar.url') .  preg_replace("/[^0-9]/", "", $username));
            // Verificar se a solicitação foi bem-sucedida (status code 2xx)
            if ($response->successful()) {
                $data = json_decode($response);
                $data = [
                    'username' => $username,
                    'nickname' => ucfirstException($data->NomeSocial),
                    'avatar' => $data->Imagem,
                ];
                return $data;
            }
        } catch (\Throwable $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function setAvatarNickname($username = null)
    {
        $UserAvatar = UserAvatar::where('username', $username)->first() ?? new UserAvatar;
        $user = User::where('username', $username)->first(['id', 'username']);

        $avatarApi = self::getAvatar($username);

        $avatar = $avatarApi['avatar'];
        $nickname = $avatarApi['nickname'];

        if ($avatar) {

            $imagemDecodificada = base64_decode($avatar);

            if ($imagemDecodificada === false) {
                throw new \Exception("Falha ao decodificar a imagem base64.");
            }

            try {
                $temporaryFilePath = 'temp/' . uniqid('avatar_', true) . '.jpg';

                Storage::disk('local')->put($temporaryFilePath, $imagemDecodificada);
                $tempFullPath = Storage::disk('local')->path($temporaryFilePath);

                $imagine = new Imagine();
                $image = $imagine->open($tempFullPath);


                $size = $image->getSize();
                $newHeight = 175;
                $newWidth = (int) ($size->getWidth() * ($newHeight / $size->getHeight()));
                $image->resize(new Box($newWidth, $newHeight));

                $imagemRedimensionada = $image->get('jpg', ['quality' => 70]);
                $imagemRedimensionada = base64_encode($imagemRedimensionada);

                unlink($tempFullPath);

                $UserAvatar->id = $user->id;
                $UserAvatar->username = $user->username;
                $UserAvatar->avatar = $imagemRedimensionada;
                $UserAvatar->nickname = $nickname;
                $UserAvatar->save();

                // $user->username = $user->username;
                // $user->avatar = $imagemRedimensionada;
                // $user->nickname = $nickname;
                // $user->save();
            } catch (\Throwable $e) {
                throw new \Exception("Erro ao processar a imagem: " . $e->getMessage());
            }
        }
    }

    public static function getMail($username)
    {
        $mail = str_replace('usr', 'ext', $username) . '@' . config('ahtlas.users.email_domain');
        return $mail;
    }

    public static function updateOrCreateExternalUser()
    {
        $users = UserExternal::whereNull('CD_MAT_ANALISTA')
            ->orderBy('DH_REGISTRO', 'asc')
            ->get();
        $date = Carbon::now();
        $hash = Hash::make(Str::uuid());

        foreach ($users as $value) {
            $username = $value->ID + 100;

            $position =  ucfirstException($value->NO_CARGO);

            $hierarchicallevel = 0;
            $hierarchicallevel = preg_match('/Supervisor/i', $position) === 1 ? 1 : $hierarchicallevel;
            $hierarchicallevel = preg_match('/Coord/i', $position) === 1 ? 2 : $hierarchicallevel;
            $hierarchicallevel = preg_match('/Geren/i', $position) === 1 ? 3 : $hierarchicallevel;
            $hierarchicallevel = preg_match('/Geren/i', $position) === 1 ? 3 : $hierarchicallevel;
            $hierarchicallevel = preg_match('/Superintedente/i', $position) === 1 ? 4 : $hierarchicallevel;
            $hierarchicallevel = preg_match('/Diretor/i', $position) === 1 ? 5 : $hierarchicallevel;

            $user = [
                'id' => crc32($username),
                'username' => '5A'. str_pad($username, 6, '0', STR_PAD_LEFT),
                'name' => ucfirstException($value->NO_NOME),
                'nickname' => ucfirstException($value->NO_NOME),
                'uf' => 'Go',
                'position' => ucfirstException($value->NO_CARGO),
                'position_summary' => ucfirstException($value->NO_CARGO),
                'sector_n1_id' => $value->CD_SETOR,
                'email' => $value->NO_EMAIL,
                'hierarchical_level' => $hierarchicallevel,
                'type' => '5A',
                'staff' => 1,
                'admission' =>  $date,
                'is_veteran' => 0,
                'start_time' => '00:00',
                'working_hours' => '00:00',
                'active' => 1,
                'status' => 'Atividade Normal',
                'password' =>  $hash,
            ];

            $newUser = User::where('email',  $value->NO_EMAIL)->first();

            if ($newUser) {
                $newUser->update([
                    'name' => ucfirstException($value->NO_NOME),
                    'nickname' => ucfirstException($value->NO_NOME),
                    'position' => ucfirstException($value->NO_CARGO),
                    'position_summary' => ucfirstException($value->NO_CARGO),
                    'sector_n1_id' => $value->CD_SETOR,
                    'password' => $hash,
                ]);
            } else {
                $newUser =  User::create($user);
            }

            UserExternal::where('NO_EMAIL', $value->NO_EMAIL)->update(
                [
                    'CD_MAT_ANALISTA' => preg_replace('/[^0-9]/', '', $username)
                ]
            );
        }
    }

    public static function updateManagerExternalUser()
    {
        $managers = UserExternal::whereNull('CD_MAT_SUPER')
            ->whereNotNull('EMAIL_SUPERVISOR')
            ->groupBy('EMAIL_SUPERVISOR')
            ->pluck('EMAIL_SUPERVISOR');

        foreach ($managers as $value) {

            $user = User::where('email',  $value)->first();

            if ($user) {
                UserExternal::where('EMAIL_SUPERVISOR', $value)
                    ->update(['CD_MAT_SUPER' => (int) str_replace('5A', '', $user->username) ]);
            }

            $teams = UserExternal::where('EMAIL_SUPERVISOR', $value)->get();

            foreach ($teams as $item) {
                 
                try {
                    $CD_MAT_ANALISTA = '5A'.str_pad($item->CD_MAT_ANALISTA, 6, '0', STR_PAD_LEFT);

                    $updateUser = User::where('username',  $CD_MAT_ANALISTA )->first();
    
                    $updateUser->manager_n1_id = $CD_MAT_ANALISTA ;
                    $updateUser->save();
    
                } catch (\Throwable $th) {
                    dump($item);
                    dump($CD_MAT_ANALISTA);
                }
              
              
            }
        }
    }
}
