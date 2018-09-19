<?php

namespace RebelCode\Storage\FuncTest\Resource\WordPress;

use Dhii\Modular\Module\ModuleInterface;
use Exception;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use RebelCode\Modular\Testing\ModuleTestCase;
use WP_Mock;

/**
 * Tests {@link Module}.
 *
 * @see   Module
 *
 * @since [*next-version*]
 */
class WpBookingsCqrsModuleTest extends ModuleTestCase
{
    /**
     * Returns the path to the module main file.
     *
     * @since [*next-version*]
     *
     * @return string The file path.
     */
    public function getModuleFilePath()
    {
        return __DIR__ . '/../../../module.php';
    }

    /**
     * Tests the `setup()` method to assert whether the resulting container contains the config.
     *
     * @since [*next-version*]
     */
    public function testSetupConfig()
    {
        /* @var $module MockObject|ModuleInterface */
        $module = $this->createModule($this->getModuleFilePath());

        $this->assertModuleHasConfig(
            $module,
            'cqrs',
            [
                'table_prefix'      => '${wpdb_prefix}',
                'bookings'          => [
                    'table'            => '${cqrs/table_prefix}bookings',
                    'field_column_map' => [
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
                    'select'           => [
                        'tables'           => [
                            'booking' => '${cqrs/bookings/table}',
                        ],
                        'field_column_map' => [
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
                        'joins'            => [
                        ],
                    ],
                    'insert'           => [
                        'table'            => '${cqrs/bookings/table}',
                        'field_column_map' => [
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
                        'insert_bulk'      => false,
                    ],
                    'update'           => [
                        'table'            => '${cqrs/bookings/table}',
                        'field_column_map' => [
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
                    ],
                    'delete'           => [
                        'table'            => '${cqrs/bookings/table}',
                        'field_column_map' => [
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
                    ],
                ],
                'sessions'          => [
                    'table'            => '${cqrs/table_prefix}sessions',
                    'field_column_map' =>
                        [
                            'id'          => 'id',
                            'start'       => 'start',
                            'end'         => 'end',
                            'service_id'  => 'service_id',
                            'resource_id' => 'resource_id',
                            'rule_id'     => 'rule_id',
                        ],
                    'select'           => [
                        'tables'           =>
                            [
                                'session' => '${cqrs/sessions/table}',
                            ],
                        'field_column_map' => [
                            'id'          => 'id',
                            'start'       => 'start',
                            'end'         => 'end',
                            'service_id'  => 'service_id',
                            'resource_id' => 'resource_id',
                            'rule_id'     => 'rule_id',
                        ],
                        'joins'            => [
                        ],
                    ],
                    'insert'           => [
                        'table'            => '${cqrs/sessions/table}',
                        'field_column_map' =>
                            [
                                'id'          => 'id',
                                'start'       => 'start',
                                'end'         => 'end',
                                'service_id'  => 'service_id',
                                'resource_id' => 'resource_id',
                                'rule_id'     => 'rule_id',
                            ],
                        'insert_bulk'      => false,
                    ],
                    'update'           => [
                        'table'            => '${cqrs/sessions/table}',
                        'field_column_map' => [
                            'id'          => 'id',
                            'start'       => 'start',
                            'end'         => 'end',
                            'service_id'  => 'service_id',
                            'resource_id' => 'resource_id',
                            'rule_id'     => 'rule_id',
                        ],
                    ],
                    'delete'           => [
                        'table'            => '${cqrs/sessions/table}',
                        'field_column_map' => [
                            'id'          => 'id',
                            'start'       => 'start',
                            'end'         => 'end',
                            'service_id'  => 'service_id',
                            'resource_id' => 'resource_id',
                            'rule_id'     => 'rule_id',
                        ],
                    ],
                ],
                'unbooked_sessions' => [
                    'table'            => '${cqrs/table_prefix}sessions',
                    'field_column_map' => [
                        'id'          => [
                            0 => 'session',
                            1 => 'id',
                        ],
                        'start'       => [
                            0 => 'session',
                            1 => 'start',
                        ],
                        'end'         => [
                            0 => 'session',
                            1 => 'end',
                        ],
                        'service_id'  => [
                            0 => 'session',
                            1 => 'service_id',
                        ],
                        'resource_id' => [
                            0 => 'session',
                            1 => 'resource_id',
                        ],
                    ],
                    'select'           => [
                        'tables'           => [
                            'session' => '${cqrs/sessions/table}',
                        ],
                        'field_column_map' => [
                            'id'          => [
                                0 => 'session',
                                1 => 'id',
                            ],
                            'start'       => [
                                0 => 'session',
                                1 => 'start',
                            ],
                            'end'         => [
                                0 => 'session',
                                1 => 'end',
                            ],
                            'service_id'  => [
                                0 => 'session',
                                1 => 'service_id',
                            ],
                            'resource_id' => [
                                0 => 'session',
                                1 => 'resource_id',
                            ],
                        ],
                        'joins'            => 'unbooked_sessions_select_join_conditions',
                    ],
                ],
                'session_rules'     => [
                    'table'            => '${cqrs/table_prefix}session_rules',
                    'field_column_map' => [
                        'id'                  => 'id',
                        'service_id'          => 'service_id',
                        'start'               => 'start',
                        'end'                 => 'end',
                        'all_day'             => 'all_day',
                        'repeat'              => 'repeat',
                        'repeat_period'       => 'repeat_period',
                        'repeat_unit'         => 'repeat_unit',
                        'repeat_until'        => 'repeat_until',
                        'repeat_until_date'   => 'repeat_until_date',
                        'repeat_until_period' => 'repeat_until_period',
                        'repeat_weekly_on'    => 'repeat_weekly_on',
                        'repeat_monthly_on'   => 'repeat_monthly_on',
                        'exclude_dates'       => 'exclude_dates',
                    ],
                    'select'           => [
                        'tables'           => [
                            'session_rule' => '${cqrs/session_rules/table}',
                        ],
                        'field_column_map' => [
                            'id'                  => 'id',
                            'service_id'          => 'service_id',
                            'start'               => 'start',
                            'end'                 => 'end',
                            'all_day'             => 'all_day',
                            'repeat'              => 'repeat',
                            'repeat_period'       => 'repeat_period',
                            'repeat_unit'         => 'repeat_unit',
                            'repeat_until'        => 'repeat_until',
                            'repeat_until_date'   => 'repeat_until_date',
                            'repeat_until_period' => 'repeat_until_period',
                            'repeat_weekly_on'    => 'repeat_weekly_on',
                            'repeat_monthly_on'   => 'repeat_monthly_on',
                            'exclude_dates'       => 'exclude_dates',
                        ],
                        'joins'            => [],
                    ],
                    'insert'           => [
                        'table'            => '${cqrs/session_rules/table}',
                        'field_column_map' => [
                            'id'                  => 'id',
                            'service_id'          => 'service_id',
                            'start'               => 'start',
                            'end'                 => 'end',
                            'all_day'             => 'all_day',
                            'repeat'              => 'repeat',
                            'repeat_period'       => 'repeat_period',
                            'repeat_unit'         => 'repeat_unit',
                            'repeat_until'        => 'repeat_until',
                            'repeat_until_date'   => 'repeat_until_date',
                            'repeat_until_period' => 'repeat_until_period',
                            'repeat_weekly_on'    => 'repeat_weekly_on',
                            'repeat_monthly_on'   => 'repeat_monthly_on',
                            'exclude_dates'       => 'exclude_dates',
                        ],
                        'insert_bulk'      => true,
                    ],
                    'update'           => [
                        'table'            => '${cqrs/session_rules/table}',
                        'field_column_map' => [
                            'id'                  => 'id',
                            'service_id'          => 'service_id',
                            'start'               => 'start',
                            'end'                 => 'end',
                            'all_day'             => 'all_day',
                            'repeat'              => 'repeat',
                            'repeat_period'       => 'repeat_period',
                            'repeat_unit'         => 'repeat_unit',
                            'repeat_until'        => 'repeat_until',
                            'repeat_until_date'   => 'repeat_until_date',
                            'repeat_until_period' => 'repeat_until_period',
                            'repeat_weekly_on'    => 'repeat_weekly_on',
                            'repeat_monthly_on'   => 'repeat_monthly_on',
                            'exclude_dates'       => 'exclude_dates',
                        ],
                    ],
                    'delete'           => [
                        'table'            => '${cqrs/session_rules/table}',
                        'field_column_map' => [
                            'id'                  => 'id',
                            'service_id'          => 'service_id',
                            'start'               => 'start',
                            'end'                 => 'end',
                            'all_day'             => 'all_day',
                            'repeat'              => 'repeat',
                            'repeat_period'       => 'repeat_period',
                            'repeat_unit'         => 'repeat_unit',
                            'repeat_until'        => 'repeat_until',
                            'repeat_until_date'   => 'repeat_until_date',
                            'repeat_until_period' => 'repeat_until_period',
                            'repeat_weekly_on'    => 'repeat_weekly_on',
                            'repeat_monthly_on'   => 'repeat_monthly_on',
                            'exclude_dates'       => 'exclude_dates',
                        ],
                    ],
                ],
                'transition_logs'   => [
                    'table'            => '${cqrs/table_prefix}transition_logs',
                    'field_column_map' => [
                        'id'         => 'id',
                        'transition' => 'transition',
                        'date'       => 'date',
                        'user_id'    => 'user_id',
                        'booking_id' => 'booking_id',
                    ],
                    'select'           => [
                        'tables'           => [
                            'transition_log' => '${cqrs/transition_logs/table}',
                        ],
                        'field_column_map' => [
                            'id'         => 'id',
                            'transition' => 'transition',
                            'date'       => 'date',
                            'user_id'    => 'user_id',
                            'booking_id' => 'booking_id',
                        ],
                        'joins'            => [],
                    ],
                    'insert'           => [
                        'table'            => '${cqrs/transition_logs/table}',
                        'field_column_map' => [
                            'id'         => 'id',
                            'transition' => 'transition',
                            'date'       => 'date',
                            'user_id'    => 'user_id',
                            'booking_id' => 'booking_id',
                        ],
                        'insert_bulk'      => false,
                    ],
                    'update'           => [
                        'table'            => '${cqrs/transition_logs/table}',
                        'field_column_map' => [
                            'id'         => 'id',
                            'transition' => 'transition',
                            'date'       => 'date',
                            'user_id'    => 'user_id',
                            'booking_id' => 'booking_id',
                        ],
                    ],
                    'delete'           => [
                        'table'            => '${cqrs/transition_logs/table}',
                        'field_column_map' => [
                            'id'         => 'id',
                            'transition' => 'transition',
                            'date'       => 'date',
                            'user_id'    => 'user_id',
                            'booking_id' => 'booking_id',
                        ],
                    ],
                ],
            ]
        );
        $this->assertModuleHasConfig(
            $module,
            'wp_bookings_cqrs',
            [
                'migrations' =>
                    [
                        'target_db_version'         => 2,
                        'db_version_option'         => 'wp_bookings_db_version',
                        'placeholder_token_start'   => '${',
                        'placeholder_token_end'     => '}',
                        'placeholder_default_value' => '',
                    ],
            ]
        );
    }

    /**
     * Tests the `bookings_select_rm` service to assert if it can be retrieved from the container and if its type is
     * correct.
     *
     * @since [*next-version*]
     */
    public function testSetupBookingsSelectRm()
    {
        /* @var $module MockObject|ModuleInterface */
        $module = $this->createModule($this->getModuleFilePath());

        $this->assertModuleHasService(
            $module,
            'bookings_select_rm',
            'Dhii\Storage\Resource\SelectCapableInterface',
            [
                'wpdb'                    => function () {
                    return $this->getMockBuilder('wpdb')->disableOriginalConstructor()->getMock();
                },
                'sql_expression_template' => function () {
                    return $this->mockInterface('Dhii\Output\TemplateInterface');
                },
                'map_factory'             => function () {
                    return $this->mockInterface('Dhii\Collection\MapFactoryInterface');
                },
            ]
        );
    }

    /**
     * Tests the `bookings_insert_rm` service to assert if it can be retrieved from the container and if its type is
     * correct.
     *
     * @since [*next-version*]
     */
    public function testSetupBookingsInsertRm()
    {
        /* @var $module MockObject|ModuleInterface */
        $module = $this->createModule($this->getModuleFilePath());

        $this->assertModuleHasService(
            $module,
            'bookings_insert_rm',
            'Dhii\Storage\Resource\InsertCapableInterface',
            [
                'wpdb' => function () {
                    return $this->getMockBuilder('wpdb')->disableOriginalConstructor()->getMock();
                },
            ]
        );
    }

    /**
     * Tests the `bookings_update_rm` service to assert if it can be retrieved from the container and if its type is
     * correct.
     *
     * @since [*next-version*]
     */
    public function testSetupBookingsUpdateRm()
    {
        /* @var $module MockObject|ModuleInterface */
        $module = $this->createModule($this->getModuleFilePath());

        $this->assertModuleHasService(
            $module,
            'bookings_update_rm',
            'Dhii\Storage\Resource\UpdateCapableInterface',
            [
                'wpdb'                    => function () {
                    return $this->getMockBuilder('wpdb')->disableOriginalConstructor()->getMock();
                },
                'sql_expression_template' => function () {
                    return $this->mockInterface('Dhii\Output\TemplateInterface');
                },
            ]
        );
    }

    /**
     * Tests the `bookings_delete_rm` service to assert if it can be retrieved from the container and if its type is
     * correct.
     *
     * @since [*next-version*]
     */
    public function testSetupBookingsDeleteRm()
    {
        /* @var $module MockObject|ModuleInterface */
        $module = $this->createModule($this->getModuleFilePath());

        $this->assertModuleHasService(
            $module,
            'bookings_delete_rm',
            'Dhii\Storage\Resource\DeleteCapableInterface',
            [
                'wpdb'                    => function () {
                    return $this->getMockBuilder('wpdb')->disableOriginalConstructor()->getMock();
                },
                'sql_expression_template' => function () {
                    return $this->mockInterface('Dhii\Output\TemplateInterface');
                },
            ]
        );
    }

    /**
     * Tests the `booking_status_select_rm` service to assert if it can be retrieved from the container and if its type
     * is correct.
     *
     * @since [*next-version*]
     */
    public function testSetupBookingStatusSelectRm()
    {
        /* @var $module MockObject|ModuleInterface */
        $module = $this->createModule($this->getModuleFilePath());

        $this->assertModuleHasService(
            $module,
            'booking_status_select_rm',
            'Dhii\Storage\Resource\SelectCapableInterface',
            [
                'wpdb'                    => function () {
                    return $this->getMockBuilder('wpdb')->disableOriginalConstructor()->getMock();
                },
                'sql_expression_template' => function () {
                    return $this->mockInterface('Dhii\Output\TemplateInterface');
                },
                'sql_expression_builder'  => function () {
                    return $this->mockInterface('RebelCode\Expression\Builder\ExpressionBuilderInterface');
                },
                'map_factory'             => function () {
                    return $this->mockInterface('Dhii\Collection\MapFactoryInterface');
                },
            ]
        );
    }

    /**
     * Tests the `transition_logs_select_rm` service to assert if it can be retrieved from the container and if its
     * type is correct.
     *
     * @since [*next-version*]
     */
    public function testSetupTransitionLogsSelectRm()
    {
        /* @var $module MockObject|ModuleInterface */
        $module = $this->createModule($this->getModuleFilePath());

        $this->assertModuleHasService(
            $module,
            'transition_logs_select_rm',
            'Dhii\Storage\Resource\SelectCapableInterface',
            [
                'wpdb'                    => function () {
                    return $this->getMockBuilder('wpdb')->disableOriginalConstructor()->getMock();
                },
                'sql_expression_template' => function () {
                    return $this->mockInterface('Dhii\Output\TemplateInterface');
                },
                'map_factory'             => function () {
                    return $this->mockInterface('Dhii\Collection\MapFactoryInterface');
                },
            ]
        );
    }

    /**
     * Tests the `transition_logs_insert_rm` service to assert if it can be retrieved from the container and if its
     * type is correct.
     *
     * @since [*next-version*]
     */
    public function testSetupTransitionLogsInsertRm()
    {
        /* @var $module MockObject|ModuleInterface */
        $module = $this->createModule($this->getModuleFilePath());

        $this->assertModuleHasService(
            $module,
            'transition_logs_insert_rm',
            'Dhii\Storage\Resource\InsertCapableInterface',
            [
                'wpdb' => function () {
                    return $this->getMockBuilder('wpdb')->disableOriginalConstructor()->getMock();
                },
            ]
        );
    }

    /**
     * Tests the `transition_logs_update_rm` service to assert if it can be retrieved from the container and if its
     * type is correct.
     *
     * @since [*next-version*]
     */
    public function testSetupTransitionLogsUpdateRm()
    {
        /* @var $module MockObject|ModuleInterface */
        $module = $this->createModule($this->getModuleFilePath());

        $this->assertModuleHasService(
            $module,
            'transition_logs_update_rm',
            'Dhii\Storage\Resource\UpdateCapableInterface',
            [
                'wpdb'                    => function () {
                    return $this->getMockBuilder('wpdb')->disableOriginalConstructor()->getMock();
                },
                'sql_expression_template' => function () {
                    return $this->mockInterface('Dhii\Output\TemplateInterface');
                },
            ]
        );
    }

    /**
     * Tests the `transition_logs_delete_rm` service to assert if it can be retrieved from the container and if its
     * type is correct.
     *
     * @since [*next-version*]
     */
    public function testSetupTransitionLogsDeleteRm()
    {
        /* @var $module MockObject|ModuleInterface */
        $module = $this->createModule($this->getModuleFilePath());

        $this->assertModuleHasService(
            $module,
            'transition_logs_delete_rm',
            'Dhii\Storage\Resource\DeleteCapableInterface',
            [
                'wpdb'                    => function () {
                    return $this->getMockBuilder('wpdb')->disableOriginalConstructor()->getMock();
                },
                'sql_expression_template' => function () {
                    return $this->mockInterface('Dhii\Output\TemplateInterface');
                },
            ]
        );
    }

    /**
     * Tests the `sessions_select_rm` service to assert if it can be retrieved from the container and if its type is
     * correct.
     *
     * @since [*next-version*]
     */
    public function testSetupSessionsSelectRm()
    {
        /* @var $module MockObject|ModuleInterface */
        $module = $this->createModule($this->getModuleFilePath());

        $this->assertModuleHasService(
            $module,
            'sessions_select_rm',
            'Dhii\Storage\Resource\SelectCapableInterface',
            [
                'wpdb'                    => function () {
                    return $this->getMockBuilder('wpdb')->disableOriginalConstructor()->getMock();
                },
                'sql_expression_template' => function () {
                    return $this->mockInterface('Dhii\Output\TemplateInterface');
                },
                'map_factory'             => function () {
                    return $this->mockInterface('Dhii\Collection\MapFactoryInterface');
                },
            ]
        );
    }

    /**
     * Tests the `unbooked_sessions_select_rm` service to assert if it can be retrieved from the container and if its
     * type is correct.
     *
     * @since [*next-version*]
     */
    public function testSetupUnbookedSessionsSelectRm()
    {
        /* @var $module MockObject|ModuleInterface */
        $module = $this->createModule($this->getModuleFilePath());

        $this->assertModuleHasService(
            $module,
            'unbooked_sessions_select_rm',
            'Dhii\Storage\Resource\SelectCapableInterface',
            [
                'wpdb'                                           => function () {
                    return $this->getMockBuilder('wpdb')->disableOriginalConstructor()->getMock();
                },
                'sql_expression_template'                        => function () {
                    return $this->mockInterface('Dhii\Output\TemplateInterface');
                },
                'sql_expression_builder'                         => function () {
                    return $this->mockInterface('RebelCode\Expression\Builder\ExpressionBuilderInterface');
                },
                'map_factory'                                    => function () {
                    return $this->mockInterface('Dhii\Collection\MapFactoryInterface');
                },
                'wp_unbooked_sessions_condition'                 => function () {
                    return $this->mockInterface('Dhii\Expression\ExpressionInterface');
                },
                'wp_unbooked_sessions_grouping_fields'           => [],
                'cqrs/unbooked_sessions/select/field_column_map' => ['entity', 'field'],
            ]
        );
    }

    /**
     * Tests the `wp_unbooked_sessions_condition` service to assert if it can be retrieved from the container and if
     * its type is correct.
     *
     * @since [*next-version*]
     */
    public function testSetupWpUnbookedSessionsCondition()
    {
        /* @var $module MockObject|ModuleInterface */
        $module = $this->createModule($this->getModuleFilePath());

        $this->assertModuleHasService(
            $module,
            'wp_unbooked_sessions_condition',
            'Dhii\Expression\ExpressionInterface',
            [
                'sql_expression_builder' => function () {
                    $mock = $this->mockInterface('RebelCode\Expression\Builder\ExpressionBuilderInterface');
                    $mock->method('__call')
                         ->willReturn($this->mockInterface('Dhii\Expression\ExpressionInterface'));

                    return $mock;
                },
            ]
        );
    }

    /**
     * Tests the `wp_unbooked_sessions_grouping_fields` service to assert if it can be retrieved from the container and
     * if its type is correct.
     *
     * @since [*next-version*]
     */
    public function testSetupWpUnbookedSessionsGroupingFields()
    {
        /* @var $module MockObject|ModuleInterface */
        $module = $this->createModule($this->getModuleFilePath());

        $this->assertModuleHasService(
            $module,
            'wp_unbooked_sessions_grouping_fields',
            'array',
            [
                'sql_expression_builder' => function () {
                    return $this->mockInterface('RebelCode\Expression\Builder\ExpressionBuilderInterface');
                },
            ]
        );
    }

    /**
     * Tests the `unbooked_sessions_select_join_conditions` service to assert if it can be retrieved from the container
     * and if its type is correct.
     *
     * @since [*next-version*]
     */
    public function testSetupUnbookedSessionsSelectJoinConditions()
    {
        /* @var $module MockObject|ModuleInterface */
        $module = $this->createModule($this->getModuleFilePath());

        $this->assertModuleHasService(
            $module,
            'unbooked_sessions_select_join_conditions',
            'array',
            [
                'sql_expression_builder' => function () {
                    return $this->mockInterface('RebelCode\Expression\Builder\ExpressionBuilderInterface');
                },
            ]
        );
    }

    /**
     * Tests the `sessions_insert_rm` service to assert if it can be retrieved from the container and if its type is
     * correct.
     *
     * @since [*next-version*]
     */
    public function testSetupSessionsInsertRm()
    {
        /* @var $module MockObject|ModuleInterface */
        $module = $this->createModule($this->getModuleFilePath());

        $this->assertModuleHasService(
            $module,
            'sessions_insert_rm',
            'Dhii\Storage\Resource\InsertCapableInterface',
            [
                'wpdb' => function () {
                    return $this->getMockBuilder('wpdb')->disableOriginalConstructor()->getMock();
                },
            ]
        );
    }

    /**
     * Tests the `sessions_update_rm` service to assert if it can be retrieved from the container and if its type is
     * correct.
     *
     * @since [*next-version*]
     */
    public function testSetupSessionsUpdateRm()
    {
        /* @var $module MockObject|ModuleInterface */
        $module = $this->createModule($this->getModuleFilePath());

        $this->assertModuleHasService(
            $module,
            'sessions_update_rm',
            'Dhii\Storage\Resource\UpdateCapableInterface',
            [
                'wpdb'                    => function () {
                    return $this->getMockBuilder('wpdb')->disableOriginalConstructor()->getMock();
                },
                'sql_expression_template' => function () {
                    return $this->mockInterface('Dhii\Output\TemplateInterface');
                },
            ]
        );
    }

    /**
     * Tests the `sessions_delete_rm` service to assert if it can be retrieved from the container and if its type is
     * correct.
     *
     * @since [*next-version*]
     */
    public function testSetupSessionsDeleteRm()
    {
        /* @var $module MockObject|ModuleInterface */
        $module = $this->createModule($this->getModuleFilePath());

        $this->assertModuleHasService($module,
            'sessions_delete_rm',
            'Dhii\Storage\Resource\DeleteCapableInterface',
            [
                'wpdb'                    => function () {
                    return $this->getMockBuilder('wpdb')->disableOriginalConstructor()->getMock();
                },
                'sql_expression_template' => function () {
                    return $this->mockInterface('Dhii\Output\TemplateInterface');
                },
            ]
        );
    }

    /**
     * Tests the `session_rules_select_rm` service to assert if it can be retrieved from the container and if its type
     * is correct.
     *
     * @since [*next-version*]
     */
    public function testSetupSessionRulesSelectRm()
    {
        /* @var $module MockObject|ModuleInterface */
        $module = $this->createModule($this->getModuleFilePath());

        $this->assertModuleHasService($module,
            'session_rules_select_rm',
            'Dhii\Storage\Resource\SelectCapableInterface',
            [
                'wpdb'                    => function () {
                    return $this->getMockBuilder('wpdb')->disableOriginalConstructor()->getMock();
                },
                'sql_expression_template' => function () {
                    return $this->mockInterface('Dhii\Output\TemplateInterface');
                },
                'map_factory'             => function () {
                    return $this->mockInterface('Dhii\Collection\MapFactoryInterface');
                },
            ]
        );
    }

    /**
     * Tests the `session_rules_insert_rm` service to assert if it can be retrieved from the container and if its type
     * is correct.
     *
     * @since [*next-version*]
     */
    public function testSetupSessionRulesInsertRm()
    {
        /* @var $module MockObject|ModuleInterface */
        $module = $this->createModule($this->getModuleFilePath());

        $this->assertModuleHasService($module,
            'session_rules_insert_rm',
            'Dhii\Storage\Resource\InsertCapableInterface',
            [
                'wpdb' => function () {
                    return $this->getMockBuilder('wpdb')->disableOriginalConstructor()->getMock();
                },
            ]
        );
    }

    /**
     * Tests the `session_rules_update_rm` service to assert if it can be retrieved from the container and if its type
     * is correct.
     *
     * @since [*next-version*]
     */
    public function testSetupSessionRulesUpdateRm()
    {
        /* @var $module MockObject|ModuleInterface */
        $module = $this->createModule($this->getModuleFilePath());

        $this->assertModuleHasService($module,
            'session_rules_update_rm',
            'Dhii\Storage\Resource\UpdateCapableInterface',
            [
                'wpdb'                    => function () {
                    return $this->getMockBuilder('wpdb')->disableOriginalConstructor()->getMock();
                },
                'sql_expression_template' => function () {
                    return $this->mockInterface('Dhii\Output\TemplateInterface');
                },
            ]
        );
    }

    /**
     * Tests the `session_rules_delete_rm` service to assert if it can be retrieved from the container and if its type
     * is correct.
     *
     * @since [*next-version*]
     */
    public function testSetupSessionRulesDeleteRm()
    {
        /* @var $module MockObject|ModuleInterface */
        $module = $this->createModule($this->getModuleFilePath());

        $this->assertModuleHasService($module,
            'session_rules_delete_rm',
            'Dhii\Storage\Resource\DeleteCapableInterface',
            [
                'wpdb'                    => function () {
                    return $this->getMockBuilder('wpdb')->disableOriginalConstructor()->getMock();
                },
                'sql_expression_template' => function () {
                    return $this->mockInterface('Dhii\Output\TemplateInterface');
                },
            ]
        );
    }

    /**
     * Tests the `wp_bookings_mysqli` service to assert if it can be retrieved from the container and if its type is
     * correct.
     *
     * @since [*next-version*]
     */
    public function testSetupWpBookingsMysqli()
    {
        /* @var $module MockObject|ModuleInterface */
        $module = $this->createModule($this->getModuleFilePath());

        // Mock WordPress database constants
        define('DB_HOST', '');
        define('DB_USER', '');
        define('DB_PASSWORD', '');
        define('DB_NAME', '');

        try {
            $this->assertModuleHasService($module,
                'wp_bookings_mysqli',
                'mysqli',
                [
                    /* Add mocked dependency services here */
                ]
            );
        } catch (Exception $e) {
            // It should fail to connect
        }
    }

    /**
     * Tests the `wp_bookings_migrator` service to assert if it can be retrieved from the container and if its type is
     * correct.
     *
     * @since [*next-version*]
     */
    public function testSetupWpBookingsMigrator()
    {
        /* @var $module MockObject|ModuleInterface */
        $module = $this->createModule($this->getModuleFilePath());

        WP_Mock::setUp();
        WP_Mock::wpFunction('get_option', [
            'times'  => 1,
            'return' => rand(0, 100),
        ]);

        $this->assertModuleHasService($module,
            'wp_bookings_migrator',
            'RebelCode\Storage\Resource\WordPress\Module\Migrator',
            [
                'wp_bookings_mysqli'                           => function () {
                    return $this->getMockBuilder('mysqli')->disableOriginalConstructor()->getMock();
                },
                'wp_bookings_sql_placeholder_template_factory' => function () {
                    return $this->mockInterface('Dhii\Output\TemplateFactoryInterface');
                },
            ]
        );

        WP_Mock::tearDown();
    }

    /**
     * Tests the `wp_bookings_sql_placeholder_template_factory` service to assert if it can be retrieved from the
     * container and if its type is correct.
     *
     * @since [*next-version*]
     */
    public function testSetupWpBookingsSqlPlaceholderTemplateFactory()
    {
        /* @var $module MockObject|ModuleInterface */
        $module = $this->createModule($this->getModuleFilePath());

        $this->assertModuleHasService($module,
            'wp_bookings_sql_placeholder_template_factory',
            'Dhii\Output\TemplateFactoryInterface',
            [
                /* Add mocked dependency services here */
            ]
        );
    }

    /**
     * Tests the `wp_bookings_cqrs_auto_migrations_handler` service to assert if it can be retrieved from the container
     * and if its type is correct.
     *
     * @since [*next-version*]
     */
    public function testSetupWpBookingsCqrsAutoMigrationsHandler()
    {
        /* @var $module MockObject|ModuleInterface */
        $module = $this->createModule($this->getModuleFilePath());

        $this->assertModuleHasService($module,
            'wp_bookings_cqrs_auto_migrations_handler',
            'Dhii\Invocation\InvocableInterface',
            [
                'wp_bookings_migrator' => function () {
                    return $this->getMockBuilder('RebelCode\Storage\Resource\WordPress\Module\Migrator')
                                ->disableOriginalConstructor()
                                ->getMock();
                },
                'event_manager'        => function () {
                    return $this->mockEventManager();
                },
                'event_factory'        => function () {
                    return $this->mockEventFactory();
                },
            ]
        );
    }

    /**
     * Tests the `transition_logs_user_id_callback` service to assert if it can be retrieved from the container and if
     * its type is correct.
     *
     * @since [*next-version*]
     */
    public function testSetupTransitionLogsUserIdCallback()
    {
        /* @var $module MockObject|ModuleInterface */
        $module = $this->createModule($this->getModuleFilePath());

        $this->assertModuleHasService($module,
            'transition_logs_user_id_callback',
            'string',
            [
                /* Add mocked dependency services here */
            ]
        );
    }

    /**
     * Tests the module's `run()` method.
     *
     * @since [*next-version*]
     */
    public function testRun()
    {
        // Create module mock and set it up
        $module  = $this->createModule($this->getModuleFilePath());
        $reflect = $this->reflect($module);
        $modCntr = $module->setup();

        // Get module's event manager
        $eventManager = $reflect->_getEventManager();

        // Mock migrations handler
        $migrationsHandler = $this->mockInterface('Dhii\Invocation\InvocableInterface');

        // Expect handlers to be attached to events
        $eventManager->expects($this->exactly(2))
                     ->method('attach')
                     ->withConsecutive(
                         ['init', $migrationsHandler],
                         ['wp_bookings_cqrs_after_migration', $this->isType('callable')]
                     );

        // Prepare dependencies
        $deps = [
            'wp_bookings_cqrs_auto_migrations_handler' => function () use ($migrationsHandler) {
                return $migrationsHandler;
            },
        ];
        // Prepare full container
        $container = $this->mockCompositeContainer([
            $this->mockContainer($deps),
            $modCntr,
        ]);

        // Run the module
        $module->run($container);
    }
}
