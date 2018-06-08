<?php

return [
    // Config for all unbooked session RMs
    'table'            => '${cqrs/table_prefix}sessions',
    'field_column_map' => $sessionsFieldColumnMap = [
        'id'          => 'id',
        'start'       => 'start',
        'end'         => 'end',
        'service_id'  => 'service_id',
        'resource_id' => 'resource_id',
        'rule_id'     => 'rule_id',
    ],

    // Config for SELECT RMs
    'select'           => [
        'tables'           => ['session' => '${cqrs/sessions/table}'],
        'field_column_map' => $sessionsFieldColumnMap,
        'joins'            => [],
    ],
];
