<?php

use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;

require dirname(__DIR__).'/vendor/autoload.php';

$dotenv = new Dotenv();
$dotenv->bootEnv(dirname(__DIR__).'/.env');

$envs = array_diff(scandir(dirname(__DIR__).'/env'), array('..', '.'));
foreach ($envs as $env) {
    if (strpos($env, 'env.local')) {
        $dotenv->overload(dirname(__DIR__).'/env/' . $env);
    } else if (strpos($env, 'env')) {
        $dotenv->load(dirname(__DIR__).'/env/' . $env);
    }
}

if ($_SERVER['APP_DEBUG']) {
    umask(0000);

    Debug::enable();
}

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
