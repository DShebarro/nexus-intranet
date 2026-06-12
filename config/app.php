<?php

use App\Core\Env;

return [
    'name'     => Env::get('APP_NAME', 'Nexus Intranet'),
    'version'  => Env::get('APP_VERSION', '3.0'),
    'timezone' => Env::get('APP_TIMEZONE', 'America/Sao_Paulo'),
    'debug'    => Env::bool('APP_DEBUG', true),
    'ai_key'   => Env::get('APP_AI_KEY', ''),
];
