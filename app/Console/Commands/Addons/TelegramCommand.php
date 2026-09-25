<?php

namespace App\Console\Commands\Addons;

use App\Models\Addons\Telegram;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        sleep(7);
        $messagers = Telegram::get();

        $url = "https://api.telegram.org/bot" . config('services.telegram.bot_token') . "/sendMessage";

        foreach ($messagers as $key => $value) {

            $telegram = Telegram::where('ID_TELEGRAM', $value->ID_TELEGRAM)->first();
            if ($telegram) {
                $telegram->delete();
            }

            $CD_GRUPO = '-' . $value['CD_GRUPO'];
            $NO_MENSAGEM = $value['NO_MENSAGEM'];

            $dados = array('chat_id' => $CD_GRUPO, 'text' =>  $NO_MENSAGEM);

            try {
                Http::withOptions([
                    'verify' => false, // Desabilita a verificação SSL
                ])->post($url, $dados);
            } catch (\Throwable $th) {
                Log::error('telegram envio', [$th->getMessage()]);
            }
        }

        return Command::SUCCESS;
    }
}
