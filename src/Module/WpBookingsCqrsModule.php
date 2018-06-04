<?php

namespace RebelCode\Storage\Resource\WordPress\Module;

use Dhii\Config\ConfigFactoryInterface;
use Dhii\Data\Container\ContainerFactoryInterface;
use Dhii\Event\EventFactoryInterface;
use Dhii\Exception\InternalException;
use Dhii\Util\Normalization\NormalizeArrayCapableTrait;
use Dhii\Util\String\StringableInterface as Stringable;
use mysqli;
use Psr\Container\ContainerInterface;
use Psr\EventManager\EventInterface;
use Psr\EventManager\EventManagerInterface;
use RebelCode\Modular\Module\AbstractBaseModule;
use RebelCode\Storage\Resource\WordPress\Wpdb\BookingStatusWpdbSelectResourceModel;
use RebelCode\Storage\Resource\WordPress\Wpdb\BookingWpdbSelectResourceModel;
use RebelCode\Storage\Resource\WordPress\Wpdb\SessionsWpdbInsertResourceModel;
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
     * The database version.
     *
     * @since [*next-version*]
     */
    const DB_VERSION = 1;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable         $key                  The module key.
     * @param string[]|Stringable[]     $dependencies         The module dependencies.
     * @param ConfigFactoryInterface    $configFactory        The config factory.
     * @param ContainerFactoryInterface $containerFactory     The container factory.
     * @param ContainerFactoryInterface $compContainerFactory The composite container factory.
     * @param EventManagerInterface     $eventManager         The event manager.
     * @param EventFactoryInterface     $eventFactory         The event factory.
     */
    public function __construct(
        $key,
        $dependencies,
        ConfigFactoryInterface $configFactory,
        ContainerFactoryInterface $containerFactory,
        ContainerFactoryInterface $compContainerFactory,
        $eventManager,
        $eventFactory
    ) {
        $this->_initModule($key, $dependencies, $configFactory, $containerFactory, $compContainerFactory);
        $this->_initModuleEvents($eventManager, $eventFactory);
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
                    return new SessionsWpdbInsertResourceModel(
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
                 *   Migration Services                                         |
                 *==============================================================*/

                /*
                 * The mysqli database connection instance.
                 *
                 * @since [*next-version*]
                 */
                'wp_bookings_mysqli'   => function (ContainerInterface $c) {
                    return new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
                },

                /*
                 * The migrator instance.
                 *
                 * @since [*next-version*]
                 */
                'wp_bookings_migrator' => function (ContainerInterface $c) {
                    return new Migrator(
                        $c->get('wp_bookings_mysqli'),
                        RC_WP_BOOKINGS_CQRS_MIGRATIONS_DIR,
                        \get_option($c->get('wp_bookings_cqrs/migrations/db_version_option'), 0),
                        $c->get('wp_bookings_sql_placeholder_template_factory'),
                        $c->get('cqrs')
                    );
                },

                /*
                 * The SQL placeholder template factory - used to create SQL templates with placeholder tokens.
                 *
                 * @since [*next-version*]
                 */
                'wp_bookings_sql_placeholder_template_factory' => function (ContainerInterface $c) {
                    return new SqlPlaceholderTemplateFactory(
                        $c->get('wp_bookings_cqrs/migrations/placeholder_token_start'),
                        $c->get('wp_bookings_cqrs/migrations/placeholder_token_end'),
                        $c->get('wp_bookings_cqrs/migrations/placeholder_default_value')
                    );
                },

                /*
                 * The auto migrations handlers.
                 *
                 * @since [*next-version*]
                 */
                'wp_bookings_cqrs_auto_migrations_handler' => function (ContainerInterface $c) {
                    return new AutoMigrationsHandler(
                        $c->get('wp_bookings_migrator'),
                        static::DB_VERSION,
                        $c->get('event_manager'),
                        $c->get('event_factory')
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
        // Handler to auto migrate to the latest DB version
        $this->_attach('init', $c->get('wp_bookings_cqrs_auto_migrations_handler'));

        // Update the database version after migrating
        $this->_attach('wp_bookings_cqrs_after_migration', function (EventInterface $event) use ($c) {
            $target = $event->getParam('target');
            $option = $c->get('wp_bookings_cqrs/migrations/db_version_option');

            \update_option($option, $target);
        });
    }
}
