<?php

namespace RebelCode\Storage\Resource\WordPress\Module;

use Dhii\Expression\ExpressionInterface;
use Dhii\Invocation\InvocableInterface;
use Dhii\Output\PlaceholderTemplateFactory;
use Dhii\Output\TemplateFactoryInterface;
use Dhii\Storage\Resource\DeleteCapableInterface;
use Dhii\Storage\Resource\InsertCapableInterface;
use Dhii\Storage\Resource\SelectCapableInterface;
use Dhii\Storage\Resource\UpdateCapableInterface;
use InvalidArgumentException;
use mysqli;
use Psr\Container\ContainerInterface;
use RebelCode\Storage\Resource\WordPress\Wpdb\BookingStatusWpdbSelectResourceModel;
use RebelCode\Storage\Resource\WordPress\Wpdb\SessionsWpdbInsertResourceModel;
use RebelCode\Storage\Resource\WordPress\Wpdb\UnbookedSessionsWpdbSelectResourceModel;
use RebelCode\Storage\Resource\WordPress\Wpdb\WpdbDeleteResourceModel;
use RebelCode\Storage\Resource\WordPress\Wpdb\WpdbInsertResourceModel;
use RebelCode\Storage\Resource\WordPress\Wpdb\WpdbSelectResourceModel;
use RebelCode\Storage\Resource\WordPress\Wpdb\WpdbUpdateResourceModel;
use Traversable;

if (!function_exists('\RebelCode\Storage\Resource\WordPress\Module\normalizeArray')) {
    function normalizeArray($input) {
        if (is_array($input)) {
            return $input;
        }

        if ($input instanceof Traversable) {
            return iterator_to_array($input);
        }

        return (array) $input;
    };
}

return [
    /*==============================================================*
     *   Booking RMs                                                |
     *==============================================================*/

    /**
     * The SELECT resource model for bookings.
     *
     * @since [*next-version*]
     *
     * @return SelectCapableInterface
     */
    'bookings_select_rm'                           => function (ContainerInterface $c) {
        return new WpdbSelectResourceModel(
            $c->get('wpdb'),
            $c->get('sql_expression_template'),
            $c->get('map_factory'),
            normalizeArray($c->get('cqrs/bookings/select/tables')),
            normalizeArray($c->get('cqrs/bookings/select/field_column_map')),
            normalizeArray($c->get('cqrs/bookings/select/joins'))
        );
    },

    /**
     * The INSERT resource model for bookings.
     *
     * @since [*next-version*]
     *
     * @return InsertCapableInterface
     */
    'bookings_insert_rm'                           => function (ContainerInterface $c) {
        return new WpdbInsertResourceModel(
            $c->get('wpdb'),
            $c->get('cqrs/bookings/insert/table'),
            normalizeArray($c->get('cqrs/bookings/insert/field_column_map')),
            $c->get('cqrs/bookings/insert/insert_bulk')
        );
    },

    /**
     * The UPDATE resource model for bookings.
     *
     * @since [*next-version*]
     *
     * @return UpdateCapableInterface
     */
    'bookings_update_rm'                           => function (ContainerInterface $c) {
        return new WpdbUpdateResourceModel(
            $c->get('wpdb'),
            $c->get('sql_expression_template'),
            $c->get('cqrs/bookings/insert/table'),
            normalizeArray($c->get('cqrs/bookings/insert/field_column_map'))
        );
    },

    /**
     * The DELETE resource model for bookings.
     *
     * @since [*next-version*]
     *
     * @return DeleteCapableInterface
     */
    'bookings_delete_rm'                           => function (ContainerInterface $c) {
        return new WpdbDeleteResourceModel(
            $c->get('wpdb'),
            $c->get('sql_expression_template'),
            $c->get('cqrs/bookings/insert/table'),
            normalizeArray($c->get('cqrs/bookings/insert/field_column_map'))
        );
    },

    /*==============================================================*
     *   Booking Status RMs                                         |
     *==============================================================*/

    /**
     * The SELECT resource model for booking statuses and their counts.
     *
     * @since [*next-version*]
     *
     * @return SelectCapableInterface
     */
    'booking_status_select_rm'                     => function (ContainerInterface $c) {
        return new BookingStatusWpdbSelectResourceModel(
            $c->get('wpdb'),
            $c->get('sql_expression_template'),
            $c->get('map_factory'),
            normalizeArray($c->get('cqrs/bookings/select/tables')),
            [
                'status'       => 'status',
                'status_count' => $c->get('sql_expression_builder')->fn(
                    'count', $c->get('sql_expression_builder')->ef('booking', 'status')
                ),
            ],
            ['status'],
            normalizeArray($c->get('cqrs/bookings/select/joins'))
        );
    },

    /*==============================================================*
     *   Booking Transition Log RMs                                 |
     *==============================================================*/

    /**
     * The SELECT resource model for transition logs.
     *
     * @since [*next-version*]
     *
     * @return SelectCapableInterface
     */
    'transition_logs_select_rm'                    => function (ContainerInterface $c) {
        return new WpdbSelectResourceModel(
            $c->get('wpdb'),
            $c->get('sql_expression_template'),
            $c->get('map_factory'),
            normalizeArray($c->get('cqrs/transition_logs/select/tables')),
            normalizeArray($c->get('cqrs/transition_logs/select/field_column_map')),
            normalizeArray($c->get('cqrs/transition_logs/select/joins'))
        );
    },

    /**
     * The INSERT resource model for transition logs.
     *
     * @since [*next-version*]
     *
     * @return InsertCapableInterface
     */
    'transition_logs_insert_rm'                    => function (ContainerInterface $c) {
        return new WpdbInsertResourceModel(
            $c->get('wpdb'),
            $c->get('cqrs/transition_logs/insert/table'),
            normalizeArray($c->get('cqrs/transition_logs/insert/field_column_map')),
            $c->get('cqrs/transition_logs/insert/insert_bulk')
        );
    },

    /**
     * The UPDATE resource model for transition logs.
     *
     * @since [*next-version*]
     *
     * @return UpdateCapableInterface
     */
    'transition_logs_update_rm'                    => function (ContainerInterface $c) {
        return new WpdbUpdateResourceModel(
            $c->get('wpdb'),
            $c->get('sql_expression_template'),
            $c->get('cqrs/transition_logs/update/table'),
            normalizeArray($c->get('cqrs/transition_logs/update/field_column_map'))
        );
    },

    /**
     * The DELETE resource model for transition logs.
     *
     * @since [*next-version*]
     *
     * @return DeleteCapableInterface
     */
    'transition_logs_delete_rm'                    => function (ContainerInterface $c) {
        return new WpdbDeleteResourceModel(
            $c->get('wpdb'),
            $c->get('sql_expression_template'),
            $c->get('cqrs/transition_logs/delete/table'),
            normalizeArray($c->get('cqrs/transition_logs/delete/field_column_map'))
        );
    },

    /*==============================================================*
     *   Session RMs                                                |
     *==============================================================*/

    /**
     * The SELECT resource model for sessions.
     *
     * @since [*next-version*]
     *
     * @return SelectCapableInterface
     */
    'sessions_select_rm'                           => function (ContainerInterface $c) {
        return new WpdbSelectResourceModel(
            $c->get('wpdb'),
            $c->get('sql_expression_template'),
            $c->get('map_factory'),
            normalizeArray($c->get('cqrs/sessions/select/tables')),
            normalizeArray($c->get('cqrs/sessions/select/field_column_map')),
            normalizeArray($c->get('cqrs/sessions/select/joins'))
        );
    },

    /**
     * The SELECT resource model for unbooked sessions.
     *
     * @since [*next-version*]
     *
     * @return SelectCapableInterface
     */
    'unbooked_sessions_select_rm'                  => function (ContainerInterface $c) {
        $joinsServiceKey = $c->get('cqrs/unbooked_sessions/select/joins');
        $fieldColumnMap  = normalizeArray($c->get('cqrs/unbooked_sessions/select/field_column_map'));

        // Turn array columns into entity fields
        $b = $c->get('sql_expression_builder');
        foreach ($fieldColumnMap as $_field => $_column) {
            try {
                $newFieldColumnMap[$_field] = $b->ef(
                    $_column[0],
                    $_column[1]
                );
            } catch (InvalidArgumentException $exception) {
                continue;
            }
        }

        return new UnbookedSessionsWpdbSelectResourceModel(
            $c->get('wpdb'),
            $c->get('sql_expression_template'),
            $c->get('map_factory'),
            normalizeArray($c->get('cqrs/unbooked_sessions/select/tables')),
            $fieldColumnMap,
            $c->get($joinsServiceKey),
            $c->get('wp_unbooked_sessions_condition'),
            $c->get('wp_unbooked_sessions_grouping_fields'),
            $b
        );
    },

    /**
     * The condition for the unbooked sessions SELECT resource model.
     *
     * @since [*next-version*]
     *
     * @return ExpressionInterface
     */
    'wp_unbooked_sessions_condition'               => function (ContainerInterface $c) {
        $b  = $c->get('sql_expression_builder');
        $bt = $c->get('cqrs/bookings/table');

        return $b->is(
            $b->ef($bt, 'id'),
            $b->lit(null)
        );
    },

    /**
     * The fields to group by for the unbooked sessions SELECT resource model.
     *
     * @since [*next-version*]
     *
     * @return array
     */
    'wp_unbooked_sessions_grouping_fields'         => function (ContainerInterface $c) {
        $b = $c->get('sql_expression_builder');

        return [
            $b->ef('session', 'id'),
        ];
    },

    /**
     * The join conditions for unbooked sessions SELECT resource model.
     *
     * @since [*next-version*]
     *
     * @return array
     */
    'unbooked_sessions_select_join_conditions'     => function (ContainerInterface $c) {
        // Expression builder
        $e = $c->get('sql_expression_builder');
        // The table names
        $b = $c->get('cqrs/bookings/table');
        $s = 'session';
        // Booking start and end fields
        $bs = $e->ef($b, 'start');
        $be = $e->ef($b, 'end');
        // Session start and end fields
        $ss = $e->ef($s, 'start');
        $se = $e->ef($s, 'end');

        return [
            // Join with booking table
            $b => $e->and(
            // With bookings that conflict
                $e->or(
                // Booking starts during session
                    $e->and($e->gte($bs, $ss), $e->lt($bs, $se)),
                    // Session starts during booking
                    $e->and($e->gte($ss, $bs), $e->lt($ss, $be))
                ),
                // AND have the same resource ID
                $e->eq(
                    $e->ef($b, 'resource_id'),
                    $e->ef($s, 'resource_id')
                )
            ),
        ];
    },

    /**
     * The INSERT resource model for sessions.
     *
     * @since [*next-version*]
     *
     * @return InsertCapableInterface
     */
    'sessions_insert_rm'                           => function (ContainerInterface $c) {
        return new SessionsWpdbInsertResourceModel(
            $c->get('wpdb'),
            $c->get('cqrs/sessions/insert/table'),
            normalizeArray($c->get('cqrs/sessions/insert/field_column_map')),
            $c->get('cqrs/sessions/insert/insert_bulk')
        );
    },

    /**
     * The UPDATE resource model for sessions.
     *
     * @since [*next-version*]
     *
     * @return UpdateCapableInterface
     */
    'sessions_update_rm'                           => function (ContainerInterface $c) {
        return new WpdbUpdateResourceModel(
            $c->get('wpdb'),
            $c->get('sql_expression_template'),
            $c->get('cqrs/sessions/update/table'),
            normalizeArray($c->get('cqrs/sessions/update/field_column_map'))
        );
    },

    /**
     * The DELETE resource model for sessions.
     *
     * @since [*next-version*]
     *
     * @return DeleteCapableInterface
     */
    'sessions_delete_rm'                           => function (ContainerInterface $c) {
        return new WpdbDeleteResourceModel(
            $c->get('wpdb'),
            $c->get('sql_expression_template'),
            $c->get('cqrs/sessions/delete/table'),
            normalizeArray($c->get('cqrs/sessions/delete/field_column_map'))
        );
    },

    /*==============================================================*
     *   Session Rules RMs                                          |
     *==============================================================*/

    /**
     * The SELECT resource model for session rules.
     *
     * @since [*next-version*]
     *
     * @return SelectCapableInterface
     */
    'session_rules_select_rm'                      => function (ContainerInterface $c) {
        return new WpdbSelectResourceModel(
            $c->get('wpdb'),
            $c->get('sql_expression_template'),
            $c->get('map_factory'),
            normalizeArray($c->get('cqrs/session_rules/select/tables')),
            normalizeArray($c->get('cqrs/session_rules/select/field_column_map')),
            normalizeArray($c->get('cqrs/session_rules/select/joins'))
        );
    },

    /**
     * The INSERT resource model for session rules.
     *
     * @since [*next-version*]
     *
     * @return InsertCapableInterface
     */
    'session_rules_insert_rm'                      => function (ContainerInterface $c) {
        return new WpdbInsertResourceModel(
            $c->get('wpdb'),
            $c->get('cqrs/session_rules/insert/table'),
            normalizeArray($c->get('cqrs/session_rules/insert/field_column_map')),
            $c->get('cqrs/session_rules/insert/insert_bulk')
        );
    },

    /**
     * The UPDATE resource model for session rules.
     *
     * @since [*next-version*]
     *
     * @return UpdateCapableInterface
     */
    'session_rules_update_rm'                      => function (ContainerInterface $c) {
        return new WpdbUpdateResourceModel(
            $c->get('wpdb'),
            $c->get('sql_expression_template'),
            $c->get('cqrs/session_rules/update/table'),
            normalizeArray($c->get('cqrs/session_rules/update/field_column_map'))
        );
    },

    /**
     * The DELETE resource model for session rules.
     *
     * @since [*next-version*]
     *
     * @return DeleteCapableInterface
     */
    'session_rules_delete_rm'                      => function (ContainerInterface $c) {
        return new WpdbDeleteResourceModel(
            $c->get('wpdb'),
            $c->get('sql_expression_template'),
            $c->get('cqrs/session_rules/delete/table'),
            normalizeArray($c->get('cqrs/session_rules/delete/field_column_map'))
        );
    },

    /*==============================================================*
     *   Migration Services                                         |
     *==============================================================*/

    /**
     * The mysqli database connection instance.
     *
     * @since [*next-version*]
     *
     * @return mysqli
     */
    'wp_bookings_mysqli'                           => function (ContainerInterface $c) {
        return new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    },

    /**
     * The migrator instance.
     *
     * @since [*next-version*]
     *
     * @return Migrator
     */
    'wp_bookings_migrator'                         => function (ContainerInterface $c) {
        return new Migrator(
            $c->get('wp_bookings_mysqli'),
            $this->_getConfig()->get('migrations_dir'),
            \get_option($c->get('wp_bookings_cqrs/migrations/db_version_option'), 0),
            $c->get('wp_bookings_sql_placeholder_template_factory'),
            $c
        );
    },

    /**
     * The SQL placeholder template factory - used to create SQL templates with placeholder tokens.
     *
     * @since [*next-version*]
     *
     * @return TemplateFactoryInterface
     */
    'wp_bookings_sql_placeholder_template_factory' => function (ContainerInterface $c) {
        return new PlaceholderTemplateFactory(
            'Dhii\Output\PlaceholderTemplate',
            $c->get('wp_bookings_cqrs/migrations/placeholder_token_start'),
            $c->get('wp_bookings_cqrs/migrations/placeholder_token_end'),
            $c->get('wp_bookings_cqrs/migrations/placeholder_default_value')
        );
    },

    /**
     * The auto migrations handlers.
     *
     * @since [*next-version*]
     *
     * @return InvocableInterface
     */
    'wp_bookings_cqrs_auto_migrations_handler'     => function (ContainerInterface $c) {
        return new AutoMigrationsHandler(
            $c->get('wp_bookings_migrator'),
            $c->get('wp_bookings_cqrs/migrations/target_db_version'),
            $c->get('event_manager'),
            $c->get('event_factory')
        );
    },

    /*==============================================================*
     *   Misc. Services                                             |
     *==============================================================*/

    /**
     * The callback that returns the ID of the user to use for new transition logs.
     *
     * @since [*next-version*]
     *
     * @return string
     */
    'transition_logs_user_id_callback'             => function (ContainerInterface $c) {
        return 'get_current_user_id';
    },
];
