<?php

use App\Core\Env;

return [
    'host'    => Env::get('DB_HOST', 'localhost'),
    'dbname'  => Env::get('DB_NAME', 'nexus_intranet'),
    'user'    => Env::get('DB_USER', 'root'),
    'pass'    => Env::get('DB_PASSWORD', ''),
    'charset' => Env::get('DB_CHARSET', 'utf8mb4'),
];
