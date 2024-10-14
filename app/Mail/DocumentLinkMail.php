<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DocumentLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public $documentLink;
    public $password;
    public $companyName;
    public $recipientName;

    /**
     * Create a new message instance.
     *
     * @param string $documentLink
     * @param string $password
     * @param string $companyName
     * @param string $recipientName
     * @return void
     */
    public function __construct($documentLink, $password, $companyName, $recipientName)
    {
        $this->documentLink = $documentLink;
        $this->password = $password;
        $this->companyName = $companyName;
        $this->recipientName = $recipientName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.document_link')
            ->subject("{$this->companyName}: Su documento está listo - Información de acceso")
            ->with([
                'companyName' => $this->companyName,
                'recipientName' => $this->recipientName,
            ]);
    }
}

