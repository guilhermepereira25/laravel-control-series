<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MailSeriesCreated extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        public string $serieName, public int $serieId, public int $seasons, public int $episodesPerSeason, public string $username)
    {
        $this->serieName = $serieName;
        $this->serieId = $serieId;
        $this->seasons = $seasons;
        $this->episodesPerSeason = $episodesPerSeason;
        $this->username = $username;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            from: new Address('guicargibar99@gmail.com', 'Guilherme Pereira'),
            subject: 'Series Created',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            markdown: 'mail.created',
            with: [
                'serieName' => $this->serieName,
                'serieId' => $this->serieId,
                'seasons' => $this->seasons,
                'episodesPerSeason' => $this->episodesPerSeason,
                'username' => $this->username
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
