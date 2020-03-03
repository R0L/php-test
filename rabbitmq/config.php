<?php
return [
    'vendor' => [
        'path' => dirname(__DIR__) . '/vendor',
    ],
    'rabbitmq' => [
        'host' => 'rabbitmq',
        'port' => '5672',
        'login' => 'guest',
        'password' => 'guest',
        'vhost' => '/'
    ]
];