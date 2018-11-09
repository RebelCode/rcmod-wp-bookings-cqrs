<?php

return [
    // Config for all session-resource RMs
    'table'            => '${cqrs/table_prefix}session_resources',
    'field_column_map' => $sessionResourcesFcMap = [
        'id'          => 'id',
        'session_id'  => 'session_id',
        'resource_id' => 'resource_id',
    ],

    // Config for SELECT RMs
    'select'           => [
        'tables'           => [
            'session_resource' => '${cqrs/session_resources/table}',
        ],
        'field_column_map' => $sessionResourcesFcMap,
        'joins'            => [],
    ],

    // Config for INSERT RMs
    'insert'           => [
        'table'            => '${cqrs/session_resources/table}',
        'field_column_map' => $sessionResourcesFcMap,
        'insert_bulk'      => true,
    ],

    // Config for UPDATE RMs
    'update'           => [
        'table'            => '${cqrs/session_resources/table}',
        'field_column_map' => $sessionResourcesFcMap,
    ],

    // Config for DELETE RMs
    'delete'           => [
        'table'            => '${cqrs/session_resources/table}',
        'field_column_map' => $sessionResourcesFcMap,
    ],
];
