<?php

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
$cfg['cqrs']['bookings']['select']['tables']           = ['bookings' => '${cqrs/bookings/table}'];
$cfg['cqrs']['bookings']['select']['field_column_map'] = $cfg['cqrs']['bookings']['field_column_map'];
$cfg['cqrs']['bookings']['select']['joins']            = [];

/*
 * Configuration for the bookings INSERT resource model.
 *
 * @since [*next-version*]
 */
$cfg['cqrs']['bookings']['insert']['table']            = '${cqrs/bookings/table}';
$cfg['cqrs']['bookings']['insert']['field_column_map'] = $cfg['cqrs']['bookings']['field_column_map'];
$cfg['cqrs']['bookings']['insert']['insert_bulk']      = true;

/*
 * Configuration for the bookings UPDATE resource model.
 *
 * @since [*next-version*]
 */
$cfg['cqrs']['bookings']['update']['table']            = '${cqrs/bookings/table}';
$cfg['cqrs']['bookings']['update']['field_column_map'] = $cfg['cqrs']['bookings']['field_column_map'];

/*
 * Configuration for the bookings DELETE resource model.
 *
 * @since [*next-version*]
 */
$cfg['cqrs']['bookings']['delete']['table']            = '${cqrs/bookings/table}';
$cfg['cqrs']['bookings']['delete']['field_column_map'] = $cfg['cqrs']['bookings']['field_column_map'];

return $cfg;
