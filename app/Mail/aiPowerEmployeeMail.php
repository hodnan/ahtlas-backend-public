<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class aiPowerEmployeeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $data;
    public $url;

    public function __construct($user, $data, $monthRef)
    {
        $this->user = $user;
        $this->data = $data;
        $this->url = env('APP_URL')."/management/myteam/aipower?manager=".$user['username']."&month_ref=".$monthRef;
    }
    
    public function envelope()
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), 'Ahtlas - Web Application'),
            subject: 'Ahtlas - Potênc-IA - Previsão de resultados',
        );
    }

    public function content()
    {
        return new Content(
            markdown: 'templates.mail.aiPowerEmployeeMail',
        );
    }

    public function attachments()
    {
        return [];
    }
}
