<?php

return [
    // Config for all session RMs
    'table'            => '${cqrs/table_prefix}sessions',
    'field_column_map' => $sessionsFieldColumnMap = [
        'id'           => ['session', 'id'],
        'start'        => ['session', 'start'],
        'end'          => ['session', 'end'],
        'service_id'   => ['session', 'service_id'],
        'rule_id'      => ['session', 'rule_id'],
        'resource_ids' => ['session', 'resource_ids']
    ],

    // Config for SELECT RMs
    'select'           => [
        'tables'           => ['session' => '${cqrs/sessions/table}'],
        'field_column_map' => $sessionsFieldColumnMap,
        'joins'            => [],
    ],

    // Config for INSERT RMs
    'insert'           => [
        'table'            => '${cqrs/sessions/table}',
        'field_column_map' => $sessionsFieldColumnMap,
        'insert_bulk'      => false,
    ],

    // Config for UPDATE RMs
    'update'           => [
        'table'            => '${cqrs/sessions/table}',
        'field_column_map' => $sessionsFieldColumnMap,
    ],

    // Config for DELETE RMs
    'delete'           => [
        'table'            => '${cqrs/sessions/table}',
        'field_column_map' => $sessionsFieldColumnMap,
    ],
];
