<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LdapUser extends Model
{
    protected $table = 'ldap_users';

    protected $fillable = [
        'guid',
        'username',
        'email',
    ];
}