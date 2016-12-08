<?php

return [
    'global'          => [
        'ignore_columns' => [
            'created_at',
            'updated_at',
            'last_login',
            'created_by',
            'updated_by',
        ],
        'username_for'   => [
            'unknown_authenticated_user'   => 'Unknown Authenticated User',
            'unknown_unauthenticated_user' => 'system',
        ],

        'table'          => [
            'table_prefix'             => 'revision_audit_',
            'table_suffix'             => '',
            'revision_field_name'      => 'rev',
            'revision_type_field_name' => 'revtype',
            'revision_table_name'      => 'revisions',
            'revision_id_field_type'   => 'bigint',
        ],
    ],
    'entity_managers' => [
        'default' => [
            'entities' => [
                \Somnambulist\EntityAudit\Tests\Fixtures\Core\AnimalAudit::class,
            ],
        ],
        'articles' => [
            'entities' => [
                \Somnambulist\EntityAudit\Tests\Fixtures\Core\ArticleAudit::class,
            ],
            'username_for' => [
                'unknown_authenticated_user'   => 'Its always Bob',
                'unknown_unauthenticated_user' => 'Its never Bob',
            ],
            'ignore_columns' => []
        ],
    ],

];
