<?php

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
$cfg['cqrs']['booking_status_logs']['select']['field_column_map'] = $cfg['cqrs']['booking_status_logs']['field_column_map'];
$cfg['cqrs']['booking_status_logs']['select']['joins']            = [];

/*
 * Configuration for the booking status logs INSERT resource model.
 *
 * @since [*next-version*]
 */
$cfg['cqrs']['booking_status_logs']['insert']['table']            = '${cqrs/booking_status_logs/table}';
$cfg['cqrs']['booking_status_logs']['insert']['field_column_map'] = $cfg['cqrs']['booking_status_logs']['field_column_map'];
$cfg['cqrs']['booking_status_logs']['insert']['insert_bulk']      = false;

/*
 * Configuration for the booking status logs UPDATE resource model.
 *
 * @since [*next-version*]
 */
$cfg['cqrs']['booking_status_logs']['update']['table']            = '${cqrs/booking_status_logs/table}';
$cfg['cqrs']['booking_status_logs']['update']['field_column_map'] = $cfg['cqrs']['booking_status_logs']['field_column_map'];

/*
 * Configuration for the booking status logs DELETE resource model.
 *
 * @since [*next-version*]
 */
$cfg['cqrs']['booking_status_logs']['delete']['table']            = '${cqrs/booking_status_logs/table}';
$cfg['cqrs']['booking_status_logs']['delete']['field_column_map'] = $cfg['cqrs']['booking_status_logs']['field_column_map'];

return $cfg;
