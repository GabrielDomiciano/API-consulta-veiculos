<?php

namespace  App\Models;

use App\Core\Database\Tabela;

/**
 * class Veiculo
 * 
 * responsavel por gerencias os dados do veiculo
 * 
 * @author Gabriel Domiciano
 */
class Veiculo {

    private $placa;

    private $headers = [];

    private $deviceToken = '74d34d51-3ed4-4412-a8d1-f80999a2f488';

    private $authorization = 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczovL3BsYXRhZm9ybWEuYXBpYnJhc2lsLmNvbS5ici9hdXRoL2xvZ2luIiwiaWF0IjoxNjkxNTM0NDc3LCJleHAiOjE3MjMwNzA0NzcsIm5iZiI6MTY5MTUzNDQ3NywianRpIjoiVWF4MWlEaXMyWHptQ253ayIsInN1YiI6IjQ1MjAiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.2kdulvwxAlOxlwjf132BWeuVEMwoYPwC_PvUR9Gb87w';

    private $body = [];

    private $url = 'https://cluster.apigratis.com';

    private $resource = 'api/v2/vehicles/dados';

    /**
     * Método responsável por setar a placa
     *
     * @return Object
     */
    public function setRequest($request) {
        $this->placa = $request->placa;

        return $this;
    }

    /**
     * Método responsável por buscar os dados do veículo
     *
     * @return Object
     */
    public function buscarDadosVeiculo() {
        $this->setHeaders()
            ->setBody()
            ->send();

        return $this;
    }

    /**
    * Método responsável por enviar a requisição para a API Grátis
    * @return self
    */
    protected function send() {
        $curl = curl_init($this->url . '/' . $this->resource);

        curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_HEADER         => false,
        CURLOPT_POST           => true,
        CURLOPT_CUSTOMREQUEST  => 'POST',
        CURLOPT_HTTPHEADER     => $this->headers,
        CURLOPT_POSTFIELDS     => json_encode($this->body),
        CURLOPT_SSL_VERIFYPEER => false
        ]);
        
        $response   = curl_exec($curl);
        $infoo      = curl_getinfo($curl);
 
        curl_close($curl);
        if ($response === false) {
            print_r(curl_error($curl)); exit;
        }
        
        //FALTA TERMINAR DE FAZER O TRATAMENTO DA RESPONSE
        // - A FAZER
    }

    /**
     * Método responsável por setar as informações do header da requisição
     *
     * @return Object
     */
    public function setHeaders(){
        $this->headers = [
            'Content-Type'  => 'application/json', 
            'DeviceToken'   => $this->deviceToken, 
            'Authorization' => $this->authorization
        ];

        return $this;
    }

    /**
     * Método responsável por setar o corpo da requisição
     *
     * @return Object
     */
    public function setBody() {
        $this->body = [
            'placa' => $this->placa
        ];

        return $this;
    }

    /**
     * Método responsável por retornar a response
     *
     * @return Array
     */
    public function getResponse() {
        return ['placa' => $this->placa];
    }
}