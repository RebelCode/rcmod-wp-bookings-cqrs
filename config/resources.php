<?php

return [
    // Config for all resource RMs
    'table'            => '${cqrs/table_prefix}resources',
    'field_column_map' => $resourcesFcMap = [
        'id'   => 'id',
        'type' => 'type',
        'name' => 'name',
    ],

    // Config for SELECT RMs
    'select'           => [
        'tables'           => [
            'booking' => '${cqrs/resources/table}',
        ],
        'field_column_map' => $resourcesFcMap,
        'joins'            => [],
    ],

    // Config for INSERT RMs
    'insert'           => [
        'table'            => '${cqrs/resources/table}',
        'field_column_map' => $resourcesFcMap,
        'insert_bulk'      => true,
    ],

    // Config for UPDATE RMs
    'update'           => [
        'table'            => '${cqrs/resources/table}',
        'field_column_map' => $resourcesFcMap,
    ],

    // Config for DELETE RMs
    'delete'           => [
        'table'            => '${cqrs/resources/table}',
        'field_column_map' => $resourcesFcMap,
    ],
];
