<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;

class WelcomeUserMail extends TenantMailable implements ShouldQueue
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
        $this->configureTenantMail(); // ✅ IMPORTANT

        return $this->subject('Welcome to UNIBS LMS')
            ->view('emails.welcome_user')
            ->with($this->data);
    }
}
