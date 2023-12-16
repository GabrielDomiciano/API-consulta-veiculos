<?php

//ROTA PADRÃO
$router->get('api', function () use ($router) {
    return 'Bem vindo a API do Gabriel';
});