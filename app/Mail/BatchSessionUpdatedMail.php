<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class BatchSessionUpdatedMail extends TenantMailable implements ShouldQueue
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
        return $this->subject('Session Updated: ' . ($this->data['session_name'] ?? '—'))
            ->view('emails.batch_session_updated')
            ->with($this->data);
    }
}
