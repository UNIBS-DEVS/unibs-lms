<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LmsClientMaster extends Model
{
    protected $connection = 'lms';
    protected $table = 'clients_master';

    protected $fillable = [
        'client_code',
        'client_name',
        'client_ship_to_address',
        'client_bill_to_address',
        'client_gst',
        'client_pan',
        'client_spoc_name',
        'client_spoc_email',
        'client_spoc_mobile',
        'status',
        'logo_path',
        'billing_rate'
    ];
}
