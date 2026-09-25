<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class ControlCenterTrackingEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $stage;
    public $subjet;
    public $url;
    /**
     * Create a new message instance.
     */
    public function __construct($stage)
    {
        $monthRef = Carbon::parse($stage['month_ref'])->format('Y-m');

        $this->stage = $stage;
        $this->subjet = "CCP - Stage: " .  $stage['stage']['id'] . " | " . $stage['date_ref'] ." [" .  $stage['sector_n1']['name'] . "] | [" .  $stage['indicator']['name'] . "]";
        $this->url = env('FRONTEND_URL')."/management/control-center/employee-results?filter=true&month_ref=".$monthRef. "&stage=".$stage['id'] ;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), 'Ahtlas - Web Application'),
            subject: $this->subjet ,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'templates.mail.control-center-tracking',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
