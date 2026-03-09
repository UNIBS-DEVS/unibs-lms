<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class TenantMiddleware
{
    public function handle($request, Closure $next)
    {
        if (session()->has('client_id')) {

            $config = DB::connection('lms')
                ->table('clients_sys_config')
                ->where('client_id', session('client_id'))
                ->first();

            if ($config) {

                Config::set('database.connections.tenant', [
                    'driver'    => 'mysql',
                    'host'      => $config->db_host,
                    'port'      => '3306',
                    'database'  => $config->db_name,
                    'username'  => $config->db_username,
                    'password'  => $config->db_password,
                    'charset'   => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'strict'    => true,
                ]);

                DB::purge('tenant');
                DB::reconnect('tenant');

                Config::set('database.default', 'tenant');
            }
        }

        return $next($request);
    }
}
