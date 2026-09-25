<?php

namespace App\Models\Addons\Assistant;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AssistantSession extends Model
{
    use HasFactory;
    protected $connection = 'addons';
    protected $table = 'assistant_sessions';

    protected $fillable = [
        'username',
        'session_id',
        'bearer',
    ];

    public function getBearer()
    {
        if (self::active()) {
            return $this->bearer;
        }

        $urlLogin = config('services.assistant.auth_url');
        $apikey = config('services.assistant.api_key');

        $response = Http::asForm()->post($urlLogin, [
            'grant_type' => 'urn:ibm:params:oauth:grant-type:apikey',
            'apikey' => $apikey,
        ]);

        if ($response->successful()) {
            if (isset($response['access_token'])) {
                return $response['access_token'];
            }
        }
    }

    public function getSession()
    {
        if (self::active()) {
            return $this->session_id;
        }

        $url = config('services.assistant.api_url') . config('services.assistant.api_version');

        $response = Http::withHeaders([
            'Authorization' => "Bearer " . $this->bearer,
            'Content-Type' => 'application/json',
        ])->send("POST", $url);

        if ($response->successful()) {
            if (isset($response['session_id'])) {
                return $response['session_id'];
            }
        }
    }

    public function active(): bool
    {
        
        $update = Carbon::parse($this->updated_at);
        $now = Carbon::now();
       
        return $update->diffInMinutes($now ) < (60*24) && $this->session_id;
       
    }

    public function sendMessage($question)
    {
        $bearer = $this->bearer;
        /** 
         * @disregard 
         */
        $username = preg_replace('/[^0-9]/', '', auth()->user()->username);
        $session = $this->session_id;
        $url = config('services.assistant.api_url') . "/$session/message" . config('services.assistant.api_version');

        $data = [
            "input" => [
                "text" => $question,
                "options" => ["async_callout" => false],
            ],
            "context" => [
                "skills" => [
                    "actions skill" => [
                        "skill_variables" => [
                            "user_id" => $username
                        ]
                    ]
                ]
            ]
        ];

        $response = Http::withHeaders([
            'Authorization' => "Bearer $bearer",
            'Content-Type' => 'application/json',
        ])->post($url, $data);

        return $response->json();
    }
}
