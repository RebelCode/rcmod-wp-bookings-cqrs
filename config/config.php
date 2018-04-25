<?php

/**
 * This file contains the configuration for the WP Bookings CQRS Module.
 *
 * @since [*next-version*]
 */

/*
 * The prefix for all table names.
 *
 * @since [*next-version*]
 */
$cfg['cqrs']['table_prefix'] = '${wpdb_prefix}';

/*
 * The configuration for bookings resource models.
 *
 * @since [*next-version*]
 */
include __DIR__ . '/bookings.php';

/*
 * The configuration for transition logs resource models.
 *
 * @since [*next-version*]
 */
include __DIR__ . '/transition_log.php';

/*
 * The configuration for sessions resource models.
 *
 * @since [*next-version*]
 */
include __DIR__ . '/sessions.php';

// The final config
return $cfg;
