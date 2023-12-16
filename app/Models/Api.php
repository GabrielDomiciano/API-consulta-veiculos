<?php

namespace App\Models;

use App\Core\Database\Tabela;

/**
 * class Api
 * 
 * responsável por retornar os valores padrões da API
 * 
 * @author Gabriel Domiciano
 */
class Api {

  /**
   * Método responsável por retornar o nome da API padrão
   *
   * @return string
   */
  public static function getNameApi() {
    $user = (new Tabela('api_name'))->select('id = 1')->fetchObject();
  
    return $user->nome; 
  }
}
