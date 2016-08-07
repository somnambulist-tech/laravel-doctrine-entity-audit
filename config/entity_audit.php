<?php

return [

    /*
     * Global Configuration
     *
     * These settings are used for all entity manager instances but can be overridden.
     */
    'global' => [
        /*
         * Ignored Columns
         *
         * Changes to these entity properties (table columns) will be ignored and not trigger
         * a new audit record. Add additional columns that don't need a specific version
         * tracking e.g.: sent_at, dismissed_at etc.
         */
        'ignore_columns' => [
            'created_at',
            'updated_at',
            'last_login',
            'created_by',
            'updated_by',
        ],

        /*
         * Default Usernames
         *
         * A UserResolver service is included that will attempt to get a "username" for the
         * currently active user, to use on the revision record. This checks for:
         *
         *  * UUID
         *  * AuthIdentifier
         *  * Entity ID (via Identifiable interface)
         *  * Name (via Nameable interface)
         *
         * If a username cannot be resolved, then unknown_authenticated_user is used.
         *
         * For all other unauth'd users, unknown_unauthenticated_user is used. This is usually
         * the system process (e.g.: on CLI or seeders etc).
         */
        'username_for' => [
            'unknown_authenticated_user'   => 'Unknown Authenticated User',
            'unknown_unauthenticated_user' => 'system',
        ],

        /*
         * Audit Table
         *
         * These set the global audit table formatting. Can be overridden per entity manager.
         */
        'table' => [
            /*
             * Optional table prefix for audit tables, e.g.: to keep them together
             */
            'table_prefix'             => 'revision_audit_',

            /*
             * Audit table suffix, appended to the entity table name
             */
            'table_suffix'             => '',

            /*
             * The name of the audit tables rev field (contains an int, usually)
             */
            'revision_field_name'      => 'rev',

            /*
             * The name of the audit tables revision type field (contains: INS, UPD, DEL)
             */
            'revision_type_field_name' => 'revtype',

            /*
             * The main revisions table name. Each UoW flush will get a new version number from this table.
             */
            'revision_table_name'      => 'revisions',

            /*
             * The main revision id type, should be at least integer or for large systems, bigint
             */
            'revision_id_field_type'   => 'bigint',
        ]
    ],

    /*
     * Entity Manager Configuration
     *
     * Auditing can be enabled on a per entity manager instance. Provide the name of the instance
     * as the main key, and then place entity classes in the entities key. The global config can be
     * overridden by adding the appropriate key.
     *
     * 'my_manager' => [
     *     'entities' => [],
     *     'table' => [
     *         'table_suffix' => '',
     *     ],
     *     'username_for => [
     *
     *     ],
     *     // etc...
     * ],
     */
    'entity_managers' => [
        'default' => [
            'entities' => [
                // Some/Entity/Classname::class,

            ],
        ],
    ],

];
