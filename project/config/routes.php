<?php

declare(strict_types=1);
/**
 * @Auth       Ucdo
 * @framework  Hyperf
 */
use Hyperf\HttpServer\Router\Router;

Router::addRoute(['GET', 'POST', 'HEAD'], '/', 'App\Controller\IndexController@index');

Router::get('/favicon.ico', function () {
    return '';
});
