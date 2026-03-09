<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;

class BatchSessionDeletedMail extends TenantMailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public array $data;

    public function __construct(array $data, string $clientCode)
    {
        // 'code' tells base class to search by client_code
        parent::__construct($clientCode, 'code');

        $this->data = $data;
    }

    public function build()
    {
        return $this->subject('Session Deleted: ' . ($this->data['session_name'] ?? '—'))
            ->view('emails.batch_sessions_deleted')
            ->with([
                'data' => $this->data
            ]);
    }
}
