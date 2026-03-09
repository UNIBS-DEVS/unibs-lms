<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;

class PasswordChangedMail extends TenantMailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public array $data;

    public function __construct(array $data, string $clientCode)
    {
        // Updated constructor (TenantMailable now accepts only clientCode)
        parent::__construct($clientCode);

        $this->data = $data;
    }

    public function build()
    {
        // ✅ REQUIRED for queued tenant mail
        $this->configureTenantMail();

        return $this->subject('Your Password Has Been Updated')
            ->view('emails.password_changed')
            ->with([
                'name'         => $this->data['name'],
                'new_password' => $this->data['new_password'],
            ]);
    }
}
