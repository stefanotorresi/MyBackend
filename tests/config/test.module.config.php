<?php

return [
    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'driverClass' => 'Doctrine\DBAL\Driver\PDOSqlite\Driver',
                'params' => [
                    'memory' => true,
                ],
            ],
        ],
        'configuration' => [
            'orm_default' => [
                'generate_proxies'  => true,
                'metadata_cache'    => 'array',
                'query_cache'       => 'array',
                'result_cache'      => 'array',
            ],
        ],
    ],
];
