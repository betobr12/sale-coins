<?php

namespace App\Services;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Email extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = (object) $data;
    }

    public function build()
    {
        $this->from($this->data->emailReceiver)->view($this->data->bladePage);
    }

}
