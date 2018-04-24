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
$cfg['cqrs']['sessions']['field_column_map']['id']          = 'id';
$cfg['cqrs']['sessions']['field_column_map']['start']       = 'start';
$cfg['cqrs']['sessions']['field_column_map']['end']         = 'end';
$cfg['cqrs']['sessions']['field_column_map']['service_id']  = 'service_id';
$cfg['cqrs']['sessions']['field_column_map']['resource_id'] = 'resource_id';
$cfg['cqrs']['sessions']['field_column_map']['rule_id']     = 'rule_id';

/*
 * Configuration for the sessions SELECT resource model.
 *
 * @since [*next-version*]
 */
$cfg['cqrs']['sessions']['select']['tables']           = ['session' => '${cqrs/sessions/table}'];
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
