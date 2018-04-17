<?php

namespace RebelCode\Storage\Resource\WordPress\Module;

use Dhii\Data\Container\ContainerFactoryInterface;
use Dhii\Exception\InternalException;
use Dhii\Util\String\StringableInterface as Stringable;
use Psr\Container\ContainerInterface;
use RebelCode\Modular\Module\AbstractBaseModule;
use RebelCode\Storage\Resource\WordPress\Wpdb\BookingWpdbInsertResourceModel;
use RebelCode\Storage\Resource\WordPress\Wpdb\BookingWpdbSelectResourceModel;
use RebelCode\Storage\Resource\WordPress\Wpdb\BookingWpdbUpdateResourceModel;
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
    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable         $key                  The module key.
     * @param string[]|Stringable[]     $dependencies         The module dependencies.
     * @param ContainerFactoryInterface $containerFactory     The container factory.
     * @param ContainerFactoryInterface $configFactory        The config factory.
     * @param ContainerFactoryInterface $compContainerFactory The composite container factory.
     */
    public function __construct(
        $key,
        $dependencies = [],
        ContainerFactoryInterface $containerFactory,
        ContainerFactoryInterface $configFactory,
        ContainerFactoryInterface $compContainerFactory
    ) {
        $this->_initModule(
            $key,
            $dependencies,
            $containerFactory,
            $configFactory,
            $compContainerFactory
        );
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
                        $c->get('cqrs/bookings/select/tables'),
                        $c->get('cqrs/bookings/select/field_column_map'),
                        $c->get('booking_status_logs_select_rm'),
                        $c->get('sql_order_factory'),
                        $c->get('sql_expression_builder'),
                        $c->get('cqrs/bookings/select/joins')
                    );
                },

                /*
                 * The INSERT resource model for bookings.
                 *
                 * @since [*next-version*]
                 */
                'bookings_insert_rm'            => function (ContainerInterface $c) {
                    return new BookingWpdbInsertResourceModel(
                        $c->get('wpdb'),
                        $c->get('cqrs/bookings/insert/table'),
                        $c->get('cqrs/bookings/insert/field_column_map'),
                        $c->get('cqrs/bookings/insert/insert_bulk'),
                        $c->get('booking_status_logs_insert_rm'),
                        $c->get('booking_status_logs_user_id_callback')
                    );
                },

                /*
                 * The UPDATE resource model for bookings.
                 *
                 * @since [*next-version*]
                 */
                'bookings_update_rm'            => function (ContainerInterface $c) {
                    return new BookingWpdbUpdateResourceModel(
                        $c->get('wpdb'),
                        $c->get('sql_expression_template'),
                        $c->get('cqrs/bookings/insert/table'),
                        $c->get('cqrs/bookings/insert/field_column_map'),
                        $c->get('booking_status_logs_insert_rm'),
                        $c->get('booking_status_logs_user_id_callback')
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
                        $c->get('cqrs/bookings/insert/field_column_map')
                    );
                },

                /*==============================================================*
                 *   Booking Status Log RMs                                     |
                 *==============================================================*/

                /*
                 * The SELECT resource model for booking status logs.
                 *
                 * @since [*next-version*]
                 */
                'booking_status_logs_select_rm' => function (ContainerInterface $c) {
                    return new WpdbSelectResourceModel(
                        $c->get('wpdb'),
                        $c->get('sql_expression_template'),
                        $c->get('cqrs/booking_status_logs/select/tables'),
                        $c->get('cqrs/booking_status_logs/select/field_column_map'),
                        $c->get('cqrs/booking_status_logs/select/joins')
                    );
                },

                /*
                 * The INSERT resource model for booking status logs.
                 *
                 * @since [*next-version*]
                 */
                'booking_status_logs_insert_rm' => function (ContainerInterface $c) {
                    return new WpdbInsertResourceModel(
                        $c->get('wpdb'),
                        $c->get('cqrs/booking_status_logs/insert/table'),
                        $c->get('cqrs/booking_status_logs/insert/field_column_map'),
                        $c->get('cqrs/booking_status_logs/insert/insert_bulk')
                    );
                },

                /*
                 * The UPDATE resource model for booking status logs.
                 *
                 * @since [*next-version*]
                 */
                'booking_status_logs_update_rm' => function (ContainerInterface $c) {
                    return new WpdbUpdateResourceModel(
                        $c->get('wpdb'),
                        $c->get('sql_expression_template'),
                        $c->get('cqrs/booking_status_logs/update/table'),
                        $c->get('cqrs/booking_status_logs/update/field_column_map')
                    );
                },

                /*
                 * The DELETE resource model for booking status logs.
                 *
                 * @since [*next-version*]
                 */
                'booking_status_logs_delete_rm' => function (ContainerInterface $c) {
                    return new WpdbDeleteResourceModel(
                        $c->get('wpdb'),
                        $c->get('sql_expression_template'),
                        $c->get('cqrs/booking_status_logs/delete/table'),
                        $c->get('cqrs/booking_status_logs/delete/field_column_map')
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
                        $c->get('cqrs/sessions/select/tables'),
                        $c->get('cqrs/sessions/select/field_column_map'),
                        $c->get('cqrs/sessions/select/joins')
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
                        $c->get('cqrs/sessions/insert/field_column_map'),
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
                        $c->get('cqrs/sessions/update/field_column_map')
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
                        $c->get('cqrs/sessions/delete/field_column_map')
                    );
                },

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
