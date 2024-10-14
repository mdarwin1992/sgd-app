<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DocumentSendingMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $sender;
    public $recipient;
    public $pageCount;
    public $documentPath;
    public $companyName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $sender, $recipient, $pageCount, $documentPath, $companyName)
    {
        $this->subject = $subject;
        $this->sender = $sender;
        $this->recipient = $recipient;
        $this->pageCount = $pageCount;
        $this->documentPath = $documentPath;
        $this->companyName = $companyName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.document-sending')
            ->subject('Nuevo documento enviado - ' . $this->companyName);
    }
}
