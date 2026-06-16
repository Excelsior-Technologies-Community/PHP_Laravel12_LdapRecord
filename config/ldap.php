<?php

return [

    'default' => env('LDAP_CONNECTION', 'default'),

    'connections' => [

        'default' => [
            'hosts' => ['ldap.forumsys.com'],
            'username' => 'cn=read-only-admin,dc=example,dc=com',
            'password' => 'password',
            'port' => 389,
            'base_dn' => 'dc=example,dc=com',
            'timeout' => 5,
            'use_ssl' => false,
            'use_tls' => false,
        ],

        'secondary' => [
            'hosts' => ['ldap-secondary.example.com'],
            'username' => 'cn=admin,dc=secondary,dc=com',
            'password' => 'secret',
            'port' => 389,
            'base_dn' => 'dc=secondary,dc=com',
            'timeout' => 5,
            'use_ssl' => false,
            'use_tls' => false,
        ],

    ],

];