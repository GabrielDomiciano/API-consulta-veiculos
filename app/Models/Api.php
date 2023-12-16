<?php

namespace App\Models;

use App\Core\Database\Tabela;

class Api {

  public static function getUsers() {
    return (new Tabela('user'))->select()->fetchObject();
  }
}
