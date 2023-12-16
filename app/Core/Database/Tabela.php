<?php

namespace App\Core\Database;

use PDO;
use PDOException;

/**
 * class Tabela
 *
 * Responsável por criar uma instancia de conexão com tabelas do banco de dados
 *
 * @author Gabriel Domiciano
 */

class Tabela{

  /**
   * Nome da tabela a ser acessada
   * @var string
   */
  protected $tabela        = null;

  /**
   * ID inserido
   * @var integer
   */
  protected $lastInsertId  = null;

  /**
   * Construtor responsável por definir os valores das propriedades da tabela
   * @method __construct
   * @param  string    $tabela        Nome da tabela no banco de dados
   */
  function __construct($tabela = null){
    $this->tabela        = $tabela;
  }

  /**
   * Método responsável por definir a tabela
   * @method setTabela
   * @param  string    $tabela        Nome da tabela no banco de dados
   */
  public function setTabela($tabela = null){
    $this->tabela        = $tabela;
  }


  /**
   * Responsável por criar a SQL de seleção (SELECT)
   * @method select
   * @param  string       $where  campos e valores do WHERE
   * @param  string       $order  campos e suas ordenações
   * @param  string       $limit  inicio,quantidade
   * @param  string       $campos nome dos campos e alias a serem retornados
   * @param  string       $group  campos em que os registros serão agrupados
   * @return \PDOStatement         PDOStatement Object
   */
  public function select($where = null,$order = null,$limit = null,$campos = '*',$group = null){
    $campos = is_null($campos)?'*':$campos;

    $where  = is_null($where)?'':'WHERE '.$where;
    $order  = is_null($order)?'':'ORDER BY '.$order;
    $limit  = is_null($limit)?'':'LIMIT '.$limit;
    $group  = is_null($group)?'':'GROUP BY '.$group;

    $query = "SELECT ".$campos." FROM ".$this->tabela." ".$where." ".$group." ".$order." ".$limit;

    return $this->query($query);

  }

  /**
   * Método responsável por criar a query de inserção (INSERT)
   * @method insert
   * @param  mixed   $dados  Array ou Objeto (objeto deve possuir o método getAllAttributes do trait GetSet)
   * @param  boolean $ignore Define se o modo de inserção será o INSERT INTO (false) ou INSERT IGNORE INTO (true)
   * @return boolean
   */
  public function insert($dados = null,$ignore = false){

    if(is_object($dados)){
      if(method_exists ($dados ,'getAllAttributes')){
        $dados = $dados->getAllAttributes(true);
      }else{
        return false;
      }
    }
    $campos = implode(',',array_keys($dados));
    $valores  = implode("','",array_values($dados));
    $ignore = $ignore ? ' IGNORE ' : '';
    $query = "INSERT ".$ignore." INTO ".$this->tabela." (".$campos.") VALUES ('".$valores."')";

    return $this->execute($query);

  }


  /**
   * Método responsável por criar a query de atualização (UPDATE)
   * @method update
   * @param  string $where Instrução WHERE do Delete
   * @param  mixed  $dados Array ou Objeto (objeto deve possuir o método getAllAttributes do trait GetSet)
   * @return boolean
   */
  public function update($where = null,$dados = null,$camposSemAspas = []){

    if(!is_null($where) and !is_numeric(trim($where))){

      if(is_object($dados)){
        if(method_exists ($dados ,'getAllAttributes')){
          $dados = $dados->getAllAttributes(true);
        }else{
          return false;
        }
      }

      $valores = [];
      foreach($dados as $key => $value){
        $valores[] = (in_array($key,$camposSemAspas)) ? ("".$key."= ".$value." ") : ("".$key."='".$value."'");
      }

      $valores = implode(',',$valores);

      $query = "UPDATE ".$this->tabela." SET ".$valores." WHERE ".$where;

      return $this->execute($query);
    }else {
      return false;
    }

  }

  /**
   * Método responsável por criar a query de atualização ou inserção (REPLACE)
   * @method replace
   * @param  mixed  $dados Array ou Objeto (objeto deve possuir o método getAllAttributes do trait GetSet)
   * IMPORTANTE: Devem ser enviados todos os campos que fazem parte da chave primária da tabela para evitar duplicação de dados
   * @return boolean
   */
  public function replace($dados = null){

    if(is_object($dados)){
      if(method_exists ($dados ,'getAllAttributes')){
        $dados = $dados->getAllAttributes(true);
      }else{
        return false;
      }
    }

    $campos = implode(',',array_keys($dados));
    $valores  = implode("','",array_values($dados));
    $query = "REPLACE INTO ".$this->tabela." (".$campos.") VALUES ('".$valores."')";

    return $this->execute($query);

  }

  /**
   * Responsável por criar a query de exclusão (DELETE)
   * @method delete
   * @param  string  $where Instrução WHERE do Delete
   * @return boolean
   */
  public function delete($where = null){
    if(!is_null($where) and !is_numeric(trim($where))){
      $query = "DELETE FROM ".$this->tabela." WHERE ".$where;
      return $this->execute($query);
    }else{
      return false;
    }
  }

  /**
   * Método responsável por executar as instruções SQLs com o método execute do PDO
   * @method execute
   * @param  string  $query Instruções SQL
   * @return boolean
   */
  public function execute($query){
    $query = $this->slugQuery($query);

    //ABRE A CONEXÃO
    $this->conectar($conexao);

    try{
			$conexao->beginTransaction();
      $resultado = $conexao->prepare($query);
      $resultado = $resultado->execute();
      $this->lastInsertId = $conexao->lastInsertId();
      $conexao->commit();

		}catch (PDOException $e) {
      $conexao->rollBack();
      $resultado = 'Serviço indisponível';

		}

    return $resultado;

  }

  /**
   * Método responsável por executar as instruções SQLs
   * @method query
   * @param string        $query Instruções SQL
   * @return \PDOStatement        PDOStatement Object
   */
  public function query($query){

    $query = $this->slugQuery($query);

    //ABRE A CONEXÃO
    $this->conectar($conexao);

    try{
			$conexao->beginTransaction();
			$resultado  = $conexao->query($query);
      $conexao->commit();

		}catch (PDOException $e) {
      $conexao->rollBack();
      $resultado = 'Serviço indisponível';
    }

    return $resultado;

  }



  /**
   * Reponsável por normatizar a query
   * @method slugQuery
   * @param string $query
   *
   */
  public function slugQuery($query){
    $query = trim($query);
    $query = preg_replace("/[\\\\]+\'/",'\\\'',$query);
    return $query;
  }

  /**
   * Método responsável por fechar a conexão com o banco de dados
   * @method desconectar
   * @param  object   $conexao
   * @return bool
   */
  public function desconectar(&$conexao){
    $conexao = null;
  }

  /**
   * Método responsável por retornar a conexão com o banco de dados
   * @method conectar
   * @param  object   $conexao
   * @return bool
   */
  public function conectar(&$conexao){

    $driver = env('DB_CONNECTION', true);
    $host   = env('DB_HOST', true);
    $user   = env('DB_USERNAME', true);
    $pass   = env('DB_PASSWORD', true);
    $name   = env('DB_DATABASE', true);

    try{
      $dsn     = $driver.":host=" . $host.";dbname=".$name.";charset=utf8";
      $conexao = new PDO($dsn, $user, $pass, []);
      $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $conexao->prepare("SET SESSION group_concat_max_len = 1000000,sql_mode='NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';")->execute();
      return true;
    }catch (PDOException $e) {
      die('Serviço indisponível' . $e->getMessage());
    }
  }

  /**
   * Responsável por retornar o último ID inserido
   * @method getLastInsertId
   * @return int
   */
  public function getLastInsertId(){
    return $this->lastInsertId;
  }

  /**
   * Método responsável por realizar uma inserção em massa no banco de dados
   * @param  array $loteDados      Array possuindo todos os dados a serem inseridos.
   * @param  bool  $ignore         Define se o insert deve possuir IGNORE ou não.
   * @param  array $camposSemAspas Campos que não devem possuir aspas.
   * @return bool
   */
  public function bulkInsert(array $loteDados, bool $ignore = false, bool $replace = false, array $camposSemAspas = []): bool {
    if (empty($loteDados)) return false;
    $colunas = $this->getColunasEntidade(reset($loteDados));

    foreach ($loteDados as $chave => $dadosEntidade) {
      $dadosEntidade     = $this->getAtributosEntidade($dadosEntidade);
      $loteDados[$chave] = $this->getValues($dadosEntidade, $camposSemAspas);
    }

    $valores = implode(",", $loteDados);
    $colunas = implode(",", $colunas);
    $ignore  = $ignore  ? ' IGNORE ' : '';
    $insert  = $replace ? 'REPLACE' : 'INSERT';
    $query   = "{$insert} {$ignore} INTO {$this->tabela} ({$colunas}) VALUES {$valores}";

    return $this->execute($query);
  }

    /**
   * Método responsável por retornar os valores de inserção já formatados para query
   * @param  array  $dadosEntidade Dados de entidade.
   * @return string
   */
  protected function getValues(array $dadosEntidade, array $camposSemAspas = []) {
    $values = implode(',', $this->adicionarAspasValoresEntidade($dadosEntidade, $camposSemAspas));
    return '('. $values .')';
  }

  /**
   * Método responsável por adicionar aspas aos valores de uma entidade sendo inserida/atualizada no banco.
   * @param  array $dadosEntidade          Dados da entidade.
   * @param  array $camposSemAspas Campos que não devem possuir aspas.
   * @return array
   */
  protected function adicionarAspasValoresEntidade(array $dadosEntidade, array $camposSemAspas = []) {
    $valores = [];
    foreach ($dadosEntidade as $key => $value) {
      $valores[] = (in_array($key, $camposSemAspas)) ? $value : "'$value'";
    }

    return $valores;
  }

  /**
   * Método responsável por retornar os atributos da entidade sendo inserida/atualizada no banco.
   * @param  array|object $dadosEntidade Dados de entidade.
   * @return array
   */
  protected function getAtributosEntidade($dadosEntidade) {
    if (!is_object($dadosEntidade)) return $dadosEntidade;

    return method_exists($dadosEntidade, 'getAllAttributes') ? $dadosEntidade->getAllAttributes(true) : [];
  }

  /**
   * Método responsável por retornar as colunas da entidade  sendo inserida/atualizada no banco.
   * @param  array|object $dadosEntidade Dados de entidade.
   * @return array
   */
  protected function getColunasEntidade($dadosEntidade) {
    if (!is_object($dadosEntidade)) return array_keys($dadosEntidade);

    return method_exists($dadosEntidade, 'getAllAttributes') ? array_keys($dadosEntidade->getAllAttributes(true)) : [];
  }
}