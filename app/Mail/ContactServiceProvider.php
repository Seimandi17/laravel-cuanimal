<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ContactServiceProvider extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $file;

    public function __construct($data, $file = null)
    {
        $this->data = $data;
        $this->file = $file;
    }

    public function build()
    {
        $email = $this->subject($this->data['subject'])
                      ->view('emails.contact-provider');

        if ($this->file) {
            $email->attach($this->file->getRealPath(), [
                'as'   => $this->file->getClientOriginalName(),
                'mime' => $this->file->getMimeType(),
            ]);
        }

        return $email;
    }
}
