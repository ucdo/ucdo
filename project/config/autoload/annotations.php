<?php

declare(strict_types=1);
/**
 * @Auth       Ucdo
 * @framework  Hyperf
 */
return [
    'scan' => [
        'paths' => [
            BASE_PATH . '/app',
        ],
        'ignore_annotations' => [
            'mixin',
        ],
    ],
];
