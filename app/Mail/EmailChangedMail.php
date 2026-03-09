<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;

class EmailChangedMail extends TenantMailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public array $data;

    public function __construct(array $data, string $clientCode)
    {
        // Updated constructor (only clientCode now)
        parent::__construct($clientCode);

        $this->data = $data;
    }

    public function build()
    {
        // ✅ REQUIRED for queued tenant mail
        $this->configureTenantMail();

        return $this->subject('Your Email Has Been Updated')
            ->view('emails.email_changed')
            ->with([
                'name'      => $this->data['name'],
                'old_email' => $this->data['old_email'],
                'new_email' => $this->data['new_email'],
            ]);
    }
}
