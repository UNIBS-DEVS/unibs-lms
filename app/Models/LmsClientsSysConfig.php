<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsClientsSysConfig extends Model
{
    protected $connection = 'lms';
    protected $table = 'clients_sys_config';

    protected $fillable = [
        'client_id',
        'db_host',
        'db_name',
        'db_username',
        'db_password',
        'smtp_host',
        'smtp_port',
        'smtp_admin_user',
        'smtp_admin_pass',
        'smtp_auth',
    ];

    public function client()
    {
        return $this->belongsTo(LmsClientMaster::class, 'client_id');
    }
}
