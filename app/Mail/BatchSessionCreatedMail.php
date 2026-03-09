<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class BatchSessionCreatedMail extends TenantMailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public array $data;

    public function __construct(array $data, string $clientCode)
    {
        parent::__construct($clientCode, 'code');

        $this->data = $data;
    }

    public function build()
    {
        return $this->subject('New Session Scheduled: ' . $this->data['session_name'])
            ->view('emails.batch_session_created')
            ->with($this->data);
    }
}
