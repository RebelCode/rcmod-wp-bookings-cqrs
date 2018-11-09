<?php

namespace RebelCode\Storage\Resource\WordPress\Wpdb;

use Dhii\Collection\MapFactoryInterface;
use Dhii\Output\TemplateInterface;
use Dhii\Storage\Resource\Sql\EntityFieldInterface;
use Dhii\Util\String\StringableInterface as Stringable;
use stdClass;
use Traversable;
use wpdb;

/**
 * A SELECT resource model specific to bookings.
 *
 * This implementation includes the resources from the relationship table and groups bookings such that a new column is
 * included that contains a comma separated list of resource IDs.
 *
 * @since [*next-version*]
 */
class BookingsSelectResourceModel extends WpdbSelectResourceModel
{
    /**
     * The name of the resource IDs aggregate column.
     *
     * @since [*next-version*]
     */
    const RESOURCES_COLUMN = 'resources';

    /**
     * The expression builder.
     *
     * @since [*next-version*]
     *
     * @var object
     */
    protected $expBuilder;

    /**
     * The fields to group by.
     *
     * @since [*next-version*]
     *
     * @var string[]|Stringable[]|EntityFieldInterface[]|stdClass|Traversable
     */
    protected $grouping;

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     *
     * @param object                                                            $expBuilder The expression builder.
     * @param string[]|Stringable[]|EntityFieldInterface[]|stdClass|Traversable $grouping   The fields to group by.
     */
    public function __construct(
        wpdb $wpdb,
        TemplateInterface $expressionTemplate,
        MapFactoryInterface $factory,
        $tables,
        $fieldColumnMap,
        $expBuilder,
        $joins = [],
        $grouping = []
    ) {
        $this->expBuilder = $expBuilder;
        $this->grouping   = $grouping;

        $fieldColumnMap[static::RESOURCES_COLUMN] = $expBuilder->fn(
            'GROUP_CONCAT',
            [$expBuilder->ef('booking_resources', 'resource_id')]
        );

        parent::__construct($wpdb, $expressionTemplate, $factory, $tables, $fieldColumnMap, $joins);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _getSqlSelectGrouping()
    {
        return $this->grouping;
    }
}
