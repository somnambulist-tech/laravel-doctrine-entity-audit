<?php
return [
    'managers' => [
        'default' => [
            'dev'           => true,
            'meta'          => env('DOCTRINE_METADATA', 'annotation'),
            'connection'    => env('DB_CONNECTION', 'mysql'),
            'namespaces'    => [
                'App'
            ],
            'paths'         => [

            ],
            'repository'    => Doctrine\ORM\EntityRepository::class,
            'proxies'       => [
                'namespace'     => false,
                'path'          => storage_path('cache/proxies'),
                'auto_generate' => false
            ],
            'events'        => [
                'listeners'   => [],
                'subscribers' => []
            ],
            'filters'       => [],
            'mapping_types' => [
                //'enum' => 'string'
            ]
        ],
        'articles' => [
            'dev'           => true,
            'meta'          => env('DOCTRINE_METADATA', 'annotation'),
            'connection'    => env('DB_CONNECTION', 'mysql'),
            'namespaces'    => [
                'App'
            ],
            'paths'         => [

            ],
            'repository'    => Doctrine\ORM\EntityRepository::class,
            'proxies'       => [
                'namespace'     => false,
                'path'          => storage_path('cache/proxies'),
                'auto_generate' => false
            ],
            'events'        => [
                'listeners'   => [],
                'subscribers' => []
            ],
            'filters'       => [],
            'mapping_types' => [
                //'enum' => 'string'
            ]
        ]
    ],
];
