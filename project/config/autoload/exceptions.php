<?php

declare(strict_types=1);
/**
 * @Auth       Ucdo
 * @framework  Hyperf
 */
return [
    'handler' => [
        'http' => [
            Hyperf\HttpServer\Exception\Handler\HttpExceptionHandler::class,
            App\Exception\Handler\AppExceptionHandler::class,
        ],
    ],
];
