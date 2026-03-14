<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'client_code' => 'required',
            'email'       => 'required|email',
            'password'    => 'required',
        ]);

        /*
        |--------------------------------------------------------------------------
        | STEP 1: Fetch client from LMS MASTER DB
        |--------------------------------------------------------------------------
        */
        $client = DB::connection('lms')
            ->table('clients_master')
            ->where('client_code', $request->client_code)
            ->where('status', 'active')
            ->first();

        if (!$client) {
            return back()->with('error', 'Invalid Client Code');
        }

        /*
        |--------------------------------------------------------------------------
        | STEP 2: Fetch tenant DB config from clients_sys_config
        |--------------------------------------------------------------------------
        */
        $config = DB::connection('lms')
            ->table('clients_sys_config')
            ->where('client_id', $client->id)
            ->first();

        if (!$config) {
            return back()->with('error', 'Tenant configuration missing');
        }

        /*
        |--------------------------------------------------------------------------
        | STEP 3: Configure Tenant DB dynamically
        |--------------------------------------------------------------------------
        */
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
        // dd($config);

        DB::purge('tenant');
        DB::reconnect('tenant');

        Config::set('database.default', 'tenant');

        /*
        |--------------------------------------------------------------------------
        | STEP 4: Attempt Login
        |--------------------------------------------------------------------------
        */
        if (Auth::attempt(
            $request->only('email', 'password'),
            $request->boolean('remember')
        )) {
            $request->session()->regenerate();

            session([
                'client_id'   => $client->id,
                'client_code' => $client->client_code,
                'tenant_db'   => $config->db_name,
            ]);

            return redirect()->route('dashboard.index');
        }

        return back()
            ->with('error', 'Invalid email or password')
            ->withInput($request->except('password'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
