<?php

/*
 * The name of the table where sessions are stored.
 *
 * @since [*next-version*]
 */
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
$cfg['cqrs']['sessions']['select']['tables']           = ['${cqrs/sessions/table}'];
$cfg['cqrs']['sessions']['select']['field_column_map'] = $cfg['cqrs']['sessions']['field_column_map'];
$cfg['cqrs']['sessions']['select']['joins']            = [];

/*
 * Configuration for the sessions INSERT resource model.
 *
 * @since [*next-version*]
 */
$cfg['cqrs']['sessions']['insert']['table']            = '${cqrs/sessions/table}';
$cfg['cqrs']['sessions']['insert']['field_column_map'] = $cfg['cqrs']['sessions']['field_column_map'];
$cfg['cqrs']['sessions']['insert']['insert_bulk']      = false;

/*
 * Configuration for the sessions UPDATE resource model.
 *
 * @since [*next-version*]
 */
$cfg['cqrs']['sessions']['update']['table']            = '${cqrs/sessions/table}';
$cfg['cqrs']['sessions']['update']['field_column_map'] = $cfg['cqrs']['sessions']['field_column_map'];

/*
 * Configuration for the sessions DELETE resource model.
 *
 * @since [*next-version*]
 */
$cfg['cqrs']['sessions']['delete']['table']            = '${cqrs/sessions/table}';
$cfg['cqrs']['sessions']['delete']['field_column_map'] = $cfg['cqrs']['sessions']['field_column_map'];

return $cfg;
