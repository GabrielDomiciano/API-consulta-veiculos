<?php

namespace App\Models;

use App\Core\Database\Tabela;

class Api {

  public static function getNameApi() {
    $user = (new Tabela('api_name'))->select('id = 1')->fetchObject();
  
    return $user->nome; 
  }
}
