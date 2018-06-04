<?php

/**
 * This file contains the configuration for the WP Bookings CQRS Module.
 *
 * @since [*next-version*]
 */

return [
    'cqrs'             => [
        'table_prefix'    => '${wpdb_prefix}',
        'bookings'        => include __DIR__ . '/bookings.php',
        'sessions'        => include __DIR__ . '/sessions.php',
        'session_rules'   => include __DIR__ . '/session_rules.php',
        'transition_logs' => include __DIR__ . '/transition_log.php',
    ],
    'wp_bookings_cqrs' => [
        'migrations' => [
            /*
             * The WordPress option name where the database version is saved.
             *
             * @since [*next-version*]
             */
            'db_version_option' => 'wp_bookings_db_version'
        ]
    ]
];
