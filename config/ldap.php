<?php

return [

    /*
    |--------------------------------------------------------------------------
    | LDAP Connections
    |--------------------------------------------------------------------------
    */
'connections' => [
    'default' => [
        'hosts' => ['ldap.forumsys.com'],           // LDAP server
        'username' => 'cn=read-only-admin,dc=example,dc=com',  // Bind DN
        'password' => 'password',                   // Bind password
        'port' => 389,
        'base_dn' => 'dc=example,dc=com',          // Base DN for searching users
        'timeout' => 5,
        'use_ssl' => false,
        'use_tls' => false,
    ],
],

];