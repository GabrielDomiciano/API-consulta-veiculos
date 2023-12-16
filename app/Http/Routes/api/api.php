<?php

use App\Models\Api;

//ROTA PADRÃO
$router->get('api', function () use ($router) {
  try {
    $response = (new Api)->getUsers();
    return json_encode($response);
  } catch (\Throwable $th) {
    return 'Error';
  }
});
