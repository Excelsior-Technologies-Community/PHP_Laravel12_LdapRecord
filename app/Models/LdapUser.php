<?php

namespace App\Models;

use LdapRecord\Models\OpenLDAP\User as LdapUserModel;

class LdapUser extends LdapUserModel
{
    protected ?string $connection = 'default';
}