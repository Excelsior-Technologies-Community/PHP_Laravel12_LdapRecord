<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class LocalLdapUser extends Authenticatable
{
    protected $table = 'ldap_users';

    protected $fillable = [
        'guid',
        'username',
        'email',
    ];

    // Optional: If you don’t have passwords in local DB
    protected $hidden = [
        // 'password',
    ];

    public $timestamps = true;
}