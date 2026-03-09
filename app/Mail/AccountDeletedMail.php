<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;

class AccountDeletedMail extends TenantMailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $name;
    public string $email;

    public function __construct(string $name, string $email, string $clientCode)
    {
        // Pass only clientCode (we removed second parameter in TenantMailable)
        parent::__construct($clientCode);

        $this->name  = $name;
        $this->email = $email;
    }

    public function build()
    {
        // ✅ VERY IMPORTANT for queue
        $this->configureTenantMail();

        return $this->subject('Your Account Has Been Deleted')
            ->view('emails.account_deleted')
            ->with([
                'name'  => $this->name,
                'email' => $this->email,
            ]);
    }
}
