<?php

namespace App\Mail;

use App\Models\LmsClientMaster;
use App\Models\LmsClientsSysConfig;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class TenantMailable extends Mailable
{
    protected string $clientCode;

    public function __construct(string $clientCode)
    {
        $this->clientCode = $clientCode;
    }

    protected function configureTenantDatabase()
    {
        $client = LmsClientMaster::where('client_code', $this->clientCode)
            ->where('status', 1)
            ->first();

        if (!$client) {

            throw new \Exception("Client not found.");
        }

        Config::set('database.connections.tenant.host', $client->db_host);
        Config::set('database.connections.tenant.database', $client->db_name);
        Config::set('database.connections.tenant.username', $client->db_username);
        Config::set('database.connections.tenant.password', $client->db_password);

        DB::purge('tenant');
        DB::reconnect('tenant');
    }

    protected function configureTenantMail()
    {
        $client = LmsClientMaster::where('client_code', $this->clientCode)
            ->where('status', 1)
            ->first();

        if (!$client) {
            throw new \Exception("Client not found.");
        }

        $smtp = LmsClientsSysConfig::where('client_id', $client->id)->first();

        if (!$smtp) {
            throw new \Exception("SMTP config not found.");
        }

        Config::set('mail.default', 'smtp');

        Config::set('mail.mailers.smtp', [
            'transport'  => 'smtp',
            'host'       => $smtp->smtp_host,
            'port'       => $smtp->smtp_port,
            'encryption' => $smtp->smtp_port == 465 ? 'ssl' : 'tls',
            'username'   => $smtp->smtp_admin_user,
            'password'   => $smtp->smtp_admin_pass,
            'timeout'    => null,
        ]);

        Config::set('mail.from.address', $smtp->smtp_admin_user);
        Config::set('mail.from.name', $client->client_name);
    }
}
