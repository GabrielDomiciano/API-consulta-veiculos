<?php

$router->get('api/teste', function () use ($router) {
    return 'Hello World';
});