<?php

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
 * The configuration for bookings resource models.
 *
 * @since [*next-version*]
 */
include __DIR__ . '/booking_status_logs.php';

/*
 * The configuration for bookings resource models.
 *
 * @since [*next-version*]
 */
include __DIR__ . '/sessions.php';

// The final config
return $cfg;
