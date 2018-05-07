<?php

return [
    // Config for all booking RMs
    'table'            => '${cqrs/table_prefix}bookings',
    'field_column_map' => $bookingsFieldColumnMap = [
        'id'          => 'id',
        'start'       => 'start',
        'end'         => 'end',
        'service_id'  => 'service_id',
        'resource_id' => 'resource_id',
        'payment_id'  => 'payment_id',
        'client_id'   => 'client_id',
        'client_tz'   => 'client_tz',
        'admin_notes' => 'admin_notes',
        'status'      => 'status',
    ],

    // Config for SELECT RMs
    'select'           => [
        'tables'           => [
            'booking' => '${cqrs/bookings/table}',
        ],
        'field_column_map' => $bookingsFieldColumnMap,
        'joins'            => [],
    ],

    // Config for INSERT RMs
    'insert'           => [
        'table'            => '${cqrs/bookings/table}',
        'field_column_map' => $bookingsFieldColumnMap,
        'insert_bulk'      => false,
    ],

    // Config for UPDATE RMs
    'update'           => [
        'table'            => '${cqrs/bookings/table}',
        'field_column_map' => $bookingsFieldColumnMap,
    ],

    // Config for DELETE RMs
    'delete'           => [
        'table'            => '${cqrs/bookings/table}',
        'field_column_map' => $bookingsFieldColumnMap,
    ],
];
