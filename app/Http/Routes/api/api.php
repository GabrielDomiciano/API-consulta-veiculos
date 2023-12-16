<?php

use App\Models\Api;

//ROTA PADRÃO
$router->get('api', function () use ($router) {
  try {
    //BUSCA O RETORNO PADRÃO PARA ENDPOINTS INVÁLIDOS
    $response = (new Api)->getNameApi();
    return $response;

  } catch (\Throwable $th) {
    return 'Error';
  }
});
