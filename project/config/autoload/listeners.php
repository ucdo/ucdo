<?php

declare(strict_types=1);
/**
 * @Auth       Ucdo
 * @framework  Hyperf
 */
return [
    Hyperf\ExceptionHandler\Listener\ErrorExceptionHandler::class,
    Hyperf\Command\Listener\FailToHandleListener::class,
];
