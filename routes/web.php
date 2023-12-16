<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
 */

use Symfony\Component\Finder\Finder;

$router->get('/', function () use ($router) {
  return 'Por favor, insira um recurso válido';
});

//ROTAS DA API - TRECHO RESPONSÁVEL POR CHAMAR AS ROTAS DA API
$require = function () use ($router) {
    $files = Finder::create()
        ->sortByName(true)
        ->in(app()->path() . '/Http/Routes/api')
        ->name('*.php');

    foreach ($files as $file) {
      require $file->getRealPath();
    }
};

$require();
