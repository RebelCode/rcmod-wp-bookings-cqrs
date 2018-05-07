<?php

namespace RebelCode\Storage\Resource\WordPress\Module;

use Dhii\Data\Container\ContainerFactoryInterface;
use Dhii\Exception\InternalException;
use Dhii\Util\Normalization\NormalizeArrayCapableTrait;
use Dhii\Util\String\StringableInterface as Stringable;
use Psr\Container\ContainerInterface;
use RebelCode\Modular\Module\AbstractBaseModule;
use RebelCode\Storage\Resource\WordPress\Wpdb\BookingStatusWpdbSelectResourceModel;
use RebelCode\Storage\Resource\WordPress\Wpdb\BookingWpdbSelectResourceModel;
use RebelCode\Storage\Resource\WordPress\Wpdb\WpdbDeleteResourceModel;
use RebelCode\Storage\Resource\WordPress\Wpdb\WpdbInsertResourceModel;
use RebelCode\Storage\Resource\WordPress\Wpdb\WpdbSelectResourceModel;
use RebelCode\Storage\Resource\WordPress\Wpdb\WpdbUpdateResourceModel;

/**
 * The WordPress Bookings CQRS Module.
 *
 * @since [*next-version*]
 */
class WpBookingsCqrsModule extends AbstractBaseModule
{
    /* @since [*next-version*] */
    use NormalizeArrayCapableTrait;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable         $key                  The module key.
     * @param string[]|Stringable[]     $dependencies         The module dependencies.
     * @param ContainerFactoryInterface $configFactory        The config factory.
     * @param ContainerFactoryInterface $containerFactory     The container factory.
     * @param ContainerFactoryInterface $compContainerFactory The composite container factory.
     */
    public function __construct(
        $key,
        $dependencies = [],
        ContainerFactoryInterface $configFactory,
        ContainerFactoryInterface $containerFactory,
        ContainerFactoryInterface $compContainerFactory
    ) {
        $this->_initModule($key, $dependencies, $configFactory, $containerFactory, $compContainerFactory);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     *
     * @throws InternalException If an error occurred while reading from the config file.
     */
    public function setup()
    {
        return $this->_setupContainer(
            $this->_loadPhpConfigFile(RC_WP_BOOKINGS_CQRS_MODULE_CONFIG_FILE),
            [
                /*==============================================================*
                 *   Booking RMs                                                |
                 *==============================================================*/

                /*
                 * The SELECT resource model for bookings.
                 *
                 * @since [*next-version*]
                 */
                'bookings_select_rm'            => function (ContainerInterface $c) {
                    return new BookingWpdbSelectResourceModel(
                        $c->get('booking_factory'),
                        $c->get('wpdb'),
                        $c->get('sql_expression_template'),
                        $this->_normalizeArray($c->get('cqrs/bookings/select/tables')),
                        $this->_normalizeArray($c->get('cqrs/bookings/select/field_column_map')),
                        $this->_normalizeArray($c->get('cqrs/bookings/select/joins'))
                    );
                },

                /*
                 * The INSERT resource model for bookings.
                 *
                 * @since [*next-version*]
                 */
                'bookings_insert_rm'            => function (ContainerInterface $c) {
                    return new WpdbInsertResourceModel(
                        $c->get('wpdb'),
                        $c->get('cqrs/bookings/insert/table'),
                        $this->_normalizeArray($c->get('cqrs/bookings/insert/field_column_map')),
                        $c->get('cqrs/bookings/insert/insert_bulk')
                    );
                },

                /*
                 * The UPDATE resource model for bookings.
                 *
                 * @since [*next-version*]
                 */
                'bookings_update_rm'            => function (ContainerInterface $c) {
                    return new WpdbUpdateResourceModel(
                        $c->get('wpdb'),
                        $c->get('sql_expression_template'),
                        $c->get('cqrs/bookings/insert/table'),
                        $this->_normalizeArray($c->get('cqrs/bookings/insert/field_column_map'))
                    );
                },

                /*
                 * The DELETE resource model for bookings.
                 *
                 * @since [*next-version*]
                 */
                'bookings_delete_rm'            => function (ContainerInterface $c) {
                    return new WpdbDeleteResourceModel(
                        $c->get('wpdb'),
                        $c->get('sql_expression_template'),
                        $c->get('cqrs/bookings/insert/table'),
                        $this->_normalizeArray($c->get('cqrs/bookings/insert/field_column_map'))
                    );
                },

                /*==============================================================*
                 *   Booking Status RMs                                         |
                 *==============================================================*/

                /*
                 * The SELECT resource model for booking statuses and their counts.
                 *
                 * @since [*next-version*]
                 */
                'booking_status_select_rm' => function(ContainerInterface $c) {
                    return new BookingStatusWpdbSelectResourceModel(
                        $c->get('wpdb'),
                        $c->get('sql_expression_template'),
                        $this->_normalizeArray($c->get('cqrs/bookings/select/tables')),
                        [
                            'status'       => 'status',
                            'status_count' => $c->get('sql_expression_builder')->fn(
                                'count', $c->get('sql_expression_builder')->ef('booking', 'status')
                            ),
                        ],
                        ['status'],
                        $this->_normalizeArray($c->get('cqrs/bookings/select/joins'))
                    );
                },

                /*==============================================================*
                 *   Booking Transition Log RMs                                 |
                 *==============================================================*/

                /*
                 * The SELECT resource model for transition logs.
                 *
                 * @since [*next-version*]
                 */
                'transition_logs_select_rm' => function (ContainerInterface $c) {
                    return new WpdbSelectResourceModel(
                        $c->get('wpdb'),
                        $c->get('sql_expression_template'),
                        $this->_normalizeArray($c->get('cqrs/transition_logs/select/tables')),
                        $this->_normalizeArray($c->get('cqrs/transition_logs/select/field_column_map')),
                        $this->_normalizeArray($c->get('cqrs/transition_logs/select/joins'))
                    );
                },

                /*
                 * The INSERT resource model for transition logs.
                 *
                 * @since [*next-version*]
                 */
                'transition_logs_insert_rm' => function (ContainerInterface $c) {
                    return new WpdbInsertResourceModel(
                        $c->get('wpdb'),
                        $c->get('cqrs/transition_logs/insert/table'),
                        $this->_normalizeArray($c->get('cqrs/transition_logs/insert/field_column_map')),
                        $c->get('cqrs/transition_logs/insert/insert_bulk')
                    );
                },

                /*
                 * The UPDATE resource model for transition logs.
                 *
                 * @since [*next-version*]
                 */
                'transition_logs_update_rm' => function (ContainerInterface $c) {
                    return new WpdbUpdateResourceModel(
                        $c->get('wpdb'),
                        $c->get('sql_expression_template'),
                        $c->get('cqrs/transition_logs/update/table'),
                        $this->_normalizeArray($c->get('cqrs/transition_logs/update/field_column_map'))
                    );
                },

                /*
                 * The DELETE resource model for transition logs.
                 *
                 * @since [*next-version*]
                 */
                'transition_logs_delete_rm' => function (ContainerInterface $c) {
                    return new WpdbDeleteResourceModel(
                        $c->get('wpdb'),
                        $c->get('sql_expression_template'),
                        $c->get('cqrs/transition_logs/delete/table'),
                        $this->_normalizeArray($c->get('cqrs/transition_logs/delete/field_column_map'))
                    );
                },

                /*==============================================================*
                 *   Session RMs                                                |
                 *==============================================================*/

                /*
                 * The SELECT resource model for sessions.
                 *
                 * @since [*next-version*]
                 */
                'sessions_select_rm'            => function (ContainerInterface $c) {
                    return new WpdbSelectResourceModel(
                        $c->get('wpdb'),
                        $c->get('sql_expression_template'),
                        $this->_normalizeArray($c->get('cqrs/sessions/select/tables')),
                        $this->_normalizeArray($c->get('cqrs/sessions/select/field_column_map')),
                        $this->_normalizeArray($c->get('cqrs/sessions/select/joins'))
                    );
                },

                /*
                 * The INSERT resource model for sessions.
                 *
                 * @since [*next-version*]
                 */
                'sessions_insert_rm'            => function (ContainerInterface $c) {
                    return new WpdbInsertResourceModel(
                        $c->get('wpdb'),
                        $c->get('cqrs/sessions/insert/table'),
                        $this->_normalizeArray($c->get('cqrs/sessions/insert/field_column_map')),
                        $c->get('cqrs/sessions/insert/insert_bulk')
                    );
                },

                /*
                 * The UPDATE resource model for sessions.
                 *
                 * @since [*next-version*]
                 */
                'sessions_update_rm'            => function (ContainerInterface $c) {
                    return new WpdbUpdateResourceModel(
                        $c->get('wpdb'),
                        $c->get('sql_expression_template'),
                        $c->get('cqrs/sessions/update/table'),
                        $this->_normalizeArray($c->get('cqrs/sessions/update/field_column_map'))
                    );
                },

                /*
                 * The DELETE resource model for sessions.
                 *
                 * @since [*next-version*]
                 */
                'sessions_delete_rm'            => function (ContainerInterface $c) {
                    return new WpdbDeleteResourceModel(
                        $c->get('wpdb'),
                        $c->get('sql_expression_template'),
                        $c->get('cqrs/sessions/delete/table'),
                        $this->_normalizeArray($c->get('cqrs/sessions/delete/field_column_map'))
                    );
                },

                /*==============================================================*
                 *   Session Rules RMs                                          |
                 *==============================================================*/

                /*
                 * The SELECT resource model for session rules.
                 *
                 * @since [*next-version*]
                 */
                'session_rules_select_rm'            => function (ContainerInterface $c) {
                    return new WpdbSelectResourceModel(
                        $c->get('wpdb'),
                        $c->get('sql_expression_template'),
                        $this->_normalizeArray($c->get('cqrs/session_rules/select/tables')),
                        $this->_normalizeArray($c->get('cqrs/session_rules/select/field_column_map')),
                        $this->_normalizeArray($c->get('cqrs/session_rules/select/joins'))
                    );
                },

                /*
                 * The INSERT resource model for session rules.
                 *
                 * @since [*next-version*]
                 */
                'session_rules_insert_rm'            => function (ContainerInterface $c) {
                    return new WpdbInsertResourceModel(
                        $c->get('wpdb'),
                        $c->get('cqrs/session_rules/insert/table'),
                        $this->_normalizeArray($c->get('cqrs/session_rules/insert/field_column_map')),
                        $c->get('cqrs/session_rules/insert/insert_bulk')
                    );
                },

                /*
                 * The UPDATE resource model for session rules.
                 *
                 * @since [*next-version*]
                 */
                'session_rules_update_rm'            => function (ContainerInterface $c) {
                    return new WpdbUpdateResourceModel(
                        $c->get('wpdb'),
                        $c->get('sql_expression_template'),
                        $c->get('cqrs/session_rules/update/table'),
                        $this->_normalizeArray($c->get('cqrs/session_rules/update/field_column_map'))
                    );
                },

                /*
                 * The DELETE resource model for session rules.
                 *
                 * @since [*next-version*]
                 */
                'session_rules_delete_rm'            => function (ContainerInterface $c) {
                    return new WpdbDeleteResourceModel(
                        $c->get('wpdb'),
                        $c->get('sql_expression_template'),
                        $c->get('cqrs/session_rules/delete/table'),
                        $this->_normalizeArray($c->get('cqrs/session_rules/delete/field_column_map'))
                    );
                },

                /*==============================================================*
                 *   Misc. Services                                             |
                 *==============================================================*/

                /*
                 * The callback that returns the ID of the user to use for new transition logs.
                 *
                 * @since [*next-version*]
                 */
                'transition_logs_user_id_callback' => function (ContainerInterface $c) {
                    return 'get_current_user_id';
                }
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function run(ContainerInterface $c = null)
    {
    }
}
