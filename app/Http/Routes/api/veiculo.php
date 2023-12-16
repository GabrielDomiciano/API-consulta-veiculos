<?php

use \Illuminate\Http\Request;
use App\Models\Veiculo;

//ROTA PADRÃO
$router->post('api/placa', function (Request $request) use ($router) {
  try {
    $response = (new Veiculo())->setRequest($request)
                            ->buscarDadosVeiculo()
                            ->getResponse();
    return $response;
  } catch (\Throwable $th) {
    return 'Error';
  }
});
