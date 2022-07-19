<?php

use App\Kernel;

//require_once dirname(__DIR__).'/TCPServer3.php';
//require_once dirname(__DIR__).'/src/Class/TCPServer.php';
//require_once dirname(__DIR__).'/src/Class/TCPClient.php';
require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
