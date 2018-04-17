<?php

/**
 * This file contains the configuration for the WP Bookings CQRS Module.
 *
 * @since [*next-version*]
 */

/*===========================================================================*\
 * Configuration for all resource models                                     *
 *                                                                           *
 * @since [*next-version*]                                                   *
\*===========================================================================*/

/*
 * The prefix for all table names.
 *
 * @since [*next-version*]
 */
$cfg['cqrs']['table_prefix'] = '${wpdb_prefix}';

/*===========================================================================*\
 * Configuration for the resource models related to bookings.                *
 *                                                                           *
 * @since [*next-version*]                                                   *
\*===========================================================================*/

/*
 * The name of the table where bookings are stored.
 *
 * @since [*next-version*]
 */
$cfg['cqrs']['bookings']['table'] = '${cqrs/table_prefix}bookings';

/*
 * The field-to-column map configuration for all booking resource models.
 *
 * @since [*next-version*]
 */
$cfg['cqrs']['bookings']['field_column_map']['id']          = 'id';
$cfg['cqrs']['bookings']['field_column_map']['start']       = 'start';
$cfg['cqrs']['bookings']['field_column_map']['end']         = 'end';
$cfg['cqrs']['bookings']['field_column_map']['service_id']  = 'service_id';
$cfg['cqrs']['bookings']['field_column_map']['resource_id'] = 'resource_id';
$cfg['cqrs']['bookings']['field_column_map']['payment_id']  = 'payment_id';
$cfg['cqrs']['bookings']['field_column_map']['client_id']   = 'client_id';
$cfg['cqrs']['bookings']['field_column_map']['client_tz']   = 'client_tz';
$cfg['cqrs']['bookings']['field_column_map']['admin_notes'] = 'admin_notes';

/*
 * Configuration for the bookings SELECT resource model.
 *
 * @since [*next-version*]
 */
$cfg['cqrs']['bookings']['select']['tables']           = ['${cqrs/bookings/table}'];
$cfg['cqrs']['bookings']['select']['field_column_map'] = '${cqrs/bookings/field_column_map}';
$cfg['cqrs']['bookings']['select']['joins']            = [];

/*
 * Configuration for the bookings INSERT resource model.
 *
 * @since [*next-version*]
 */
$cfg['cqrs']['bookings']['insert']['table']            = '${cqrs/bookings/table}';
$cfg['cqrs']['bookings']['insert']['field_column_map'] = '${cqrs/bookings/field_column_map}';
$cfg['cqrs']['bookings']['insert']['insert_bulk']      = true;

/*
 * Configuration for the bookings UPDATE resource model.
 *
 * @since [*next-version*]
 */
$cfg['cqrs']['bookings']['update']['table']            = '${cqrs/bookings/table}';
$cfg['cqrs']['bookings']['update']['field_column_map'] = '${cqrs/bookings/field_column_map}';

/*
 * Configuration for the bookings DELETE resource model.
 *
 * @since [*next-version*]
 */
$cfg['cqrs']['bookings']['delete']['table']            = '${cqrs/bookings/table}';
$cfg['cqrs']['bookings']['delete']['field_column_map'] = '${cqrs/bookings/field_column_map}';

/*===========================================================================*\
 * Configuration for the resource models related to booking status logs.     *
 *                                                                           *
 * @since [*next-version*]                                                   *
\*===========================================================================*/

$cfg['cqrs']['booking_status_logs']['table'] = '${cqrs/table_prefix}booking_status_logs';

/*
 * The field-to-column map configuration for all booking status log resource models.
 *
 * @since [*next-version*]
 */
$cfg['cqrs']['booking_status_logs']['field_column_map']['id']         = 'id';
$cfg['cqrs']['booking_status_logs']['field_column_map']['name']       = 'name';
$cfg['cqrs']['booking_status_logs']['field_column_map']['date']       = 'date';
$cfg['cqrs']['booking_status_logs']['field_column_map']['user_id']    = 'user_id';
$cfg['cqrs']['booking_status_logs']['field_column_map']['booking_id'] = 'booking_id';

/*
 * Configuration for the booking status logs SELECT resource model.
 *
 * @since [*next-version*]
 */
$cfg['cqrs']['booking_status_logs']['select']['tables']           = ['${cqrs/booking_status_logs/table}'];
$cfg['cqrs']['booking_status_logs']['select']['field_column_map'] = '${cqrs/booking_status_logs/field_column_map}';
$cfg['cqrs']['booking_status_logs']['select']['joins']            = [];

/*
 * Configuration for the booking status logs INSERT resource model.
 *
 * @since [*next-version*]
 */
$cfg['cqrs']['booking_status_logs']['insert']['table']            = '${cqrs/booking_status_logs/table}';
$cfg['cqrs']['booking_status_logs']['insert']['field_column_map'] = '${cqrs/booking_status_logs/field_column_map}';
$cfg['cqrs']['booking_status_logs']['insert']['insert_bulk']      = false;

/*
 * Configuration for the booking status logs UPDATE resource model.
 *
 * @since [*next-version*]
 */
$cfg['cqrs']['booking_status_logs']['update']['table']            = '${cqrs/booking_status_logs/table}';
$cfg['cqrs']['booking_status_logs']['update']['field_column_map'] = '${cqrs/booking_status_logs/field_column_map}';

/*
 * Configuration for the booking status logs DELETE resource model.
 *
 * @since [*next-version*]
 */
$cfg['cqrs']['booking_status_logs']['delete']['table']            = '${cqrs/booking_status_logs/table}';
$cfg['cqrs']['booking_status_logs']['delete']['field_column_map'] = '${cqrs/booking_status_logs/field_column_map}';

/*===========================================================================*\
 * Configuration for the resource models related to sessions.                *
 *                                                                           *
 * @since [*next-version*]                                                   *
\*===========================================================================*/

$cfg['cqrs']['sessions']['table'] = '${cqrs/table_prefix}sessions';

/*
 * The field-to-column map configuration for all session resource models.
 *
 * @since [*next-version*]
 */
$cfg['cqrs']['sessions']['field_column_map']['id']         = 'id';
$cfg['cqrs']['sessions']['field_column_map']['start']      = 'name';
$cfg['cqrs']['sessions']['field_column_map']['end']        = 'date';
$cfg['cqrs']['sessions']['field_column_map']['service_id'] = 'user_id';
$cfg['cqrs']['sessions']['field_column_map']['rule_id']    = 'booking_id';

/*
 * Configuration for the sessions SELECT resource model.
 *
 * @since [*next-version*]
 */
$cfg['cqrs']['sessions']['select']['table']            = '${cqrs/sessions/table}';
$cfg['cqrs']['sessions']['select']['field_column_map'] = '${cqrs/sessions/field_column_map}';
$cfg['cqrs']['sessions']['select']['joins']            = [];

/*
 * Configuration for the sessions INSERT resource model.
 *
 * @since [*next-version*]
 */
$cfg['cqrs']['sessions']['insert']['table']            = '${cqrs/sessions/table}';
$cfg['cqrs']['sessions']['insert']['field_column_map'] = '${cqrs/sessions/field_column_map}';
$cfg['cqrs']['sessions']['insert']['insert_bulk']      = false;

/*
 * Configuration for the sessions UPDATE resource model.
 *
 * @since [*next-version*]
 */
$cfg['cqrs']['sessions']['update']['table']            = '${cqrs/sessions/table}';
$cfg['cqrs']['sessions']['update']['field_column_map'] = '${cqrs/sessions/field_column_map}';

/*
 * Configuration for the sessions DELETE resource model.
 *
 * @since [*next-version*]
 */
$cfg['cqrs']['sessions']['delete']['table']            = '${cqrs/sessions/table}';
$cfg['cqrs']['sessions']['delete']['field_column_map'] = '${cqrs/sessions/field_column_map}';

/*===========================================================================*\
 * Final Configuration                                                       *
 *                                                                           *
 * @since [*next-version*]                                                   *
\*===========================================================================*/

return $cfg;
