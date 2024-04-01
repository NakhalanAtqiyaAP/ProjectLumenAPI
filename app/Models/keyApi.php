<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class keyApi extends Model
{
    protected $fillable = ['user_id', 'key', 'level', 'ignore_limits', 'is_private_key', 'ip_addresses', 'date_created'];
}
