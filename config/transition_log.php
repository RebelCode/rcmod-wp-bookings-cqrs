<?php

/*
 * The name of the table where booking transition logs are stored.
 *
 * @since [*next-version*]
 */
$cfg['cqrs']['transition_logs']['table'] = '${cqrs/table_prefix}transition_logs';

/*
 * The field-to-column map configuration for all booking transition log resource models.
 *
 * @since [*next-version*]
 */
$cfg['cqrs']['transition_logs']['field_column_map']['id']         = 'id';
$cfg['cqrs']['transition_logs']['field_column_map']['transition'] = 'transition';
$cfg['cqrs']['transition_logs']['field_column_map']['date']       = 'date';
$cfg['cqrs']['transition_logs']['field_column_map']['user_id']    = 'user_id';
$cfg['cqrs']['transition_logs']['field_column_map']['booking_id'] = 'booking_id';

/*
 * Configuration for the booking transition logs SELECT resource model.
 *
 * @since [*next-version*]
 */
$cfg['cqrs']['transition_logs']['select']['tables']           = ['transition_log' => '${cqrs/transition_logs/table}'];
$cfg['cqrs']['transition_logs']['select']['field_column_map'] = $cfg['cqrs']['transition_logs']['field_column_map'];
$cfg['cqrs']['transition_logs']['select']['joins']            = [];

/*
 * Configuration for the booking transition logs INSERT resource model.
 *
 * @since [*next-version*]
 */
$cfg['cqrs']['transition_logs']['insert']['table']            = '${cqrs/transition_logs/table}';
$cfg['cqrs']['transition_logs']['insert']['field_column_map'] = $cfg['cqrs']['transition_logs']['field_column_map'];
$cfg['cqrs']['transition_logs']['insert']['insert_bulk']      = false;

/*
 * Configuration for the booking transition logs UPDATE resource model.
 *
 * @since [*next-version*]
 */
$cfg['cqrs']['transition_logs']['update']['table']            = '${cqrs/transition_logs/table}';
$cfg['cqrs']['transition_logs']['update']['field_column_map'] = $cfg['cqrs']['transition_logs']['field_column_map'];

/*
 * Configuration for the booking transition logs DELETE resource model.
 *
 * @since [*next-version*]
 */
$cfg['cqrs']['transition_logs']['delete']['table']            = '${cqrs/transition_logs/table}';
$cfg['cqrs']['transition_logs']['delete']['field_column_map'] = $cfg['cqrs']['transition_logs']['field_column_map'];

return $cfg;
