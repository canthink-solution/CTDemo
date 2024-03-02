<?php

/*
|--------------------------------------------------------------------------
| Mailer
|--------------------------------------------------------------------------
*/
$config['mail'] = [
    'driver' => 'smtp', // smtp or mailgun (need to register package for driver)
    'host' => 'smtp.gmail.com',
    'port' => 587,
    'username' => '',
    'password' => '',
    'encryption' => 'TLS',
    'from_email' => '',
    'from_name' => '',
    'debug' => TRUE, // TRUE/FALSE, use for smtp driver only
];
