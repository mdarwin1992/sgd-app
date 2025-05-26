<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DocumentResponseMail extends Mailable
{
    use Queueable, SerializesModels;

    public $responseLink;
    public $password;
    public $companyName;

    /**
     * Create a new message instance.
     *
     * @param string $responseLink
     * @param string $password
     * @param string $companyName
     * @return void
     */
    public function __construct($responseLink, $password, $companyName)
    {
        $this->responseLink = $responseLink;
        $this->password = $password;
        $this->companyName = $companyName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.respuestaLink')
            ->subject("{$this->companyName}: Respuesta a su documento - Información de acceso")
            ->with([
                'responseLink' => $this->responseLink,
                'password' => $this->password,
                'companyName' => $this->companyName,
            ]);
    }
}
