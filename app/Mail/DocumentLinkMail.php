<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DocumentLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public $documentLink;

    public function __construct($documentLink)
    {
        $this->documentLink = $documentLink;
    }

    public function build()
    {
        return $this->view('emails.document_link')
            ->with(['documentLink' => $this->documentLink]);

    }
}
