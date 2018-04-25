<?php

namespace RebelCode\Storage\Resource\WordPress\Wpdb;

use Dhii\Data\Container\ContainerHasCapableTrait;
use Dhii\Data\Container\ContainerSetCapableTrait;
use Dhii\Expression\LogicalExpressionInterface;
use Dhii\Factory\FactoryInterface;
use Dhii\Output\TemplateInterface;
use Dhii\Storage\Resource\SelectCapableInterface;
use Dhii\Storage\Resource\Sql\OrderInterface;
use Dhii\Util\String\StringableInterface as Stringable;
use RebelCode\Bookings\BookingFactoryInterface;
use stdClass;
use Traversable;
use wpdb;

/**
 * A resource model for bookings stored in a custom table, with the status being retrieved from a separate, status log
 * table. Fetching bookings involves fetching the corresponding status log entry and combining the data.
 *
 * @since [*next-version*]
 */
class BookingWpdbSelectResourceModel extends AbstractBaseWpdbSelectResourceModel
{
    /*
     * @since [*next-version*]
     */
    use ContainerSetCapableTrait;

    /*
     * @since [*next-version*]
     */
    use ContainerHasCapableTrait;

    /**
     * The booking factory.
     *
     * @since [*next-version*]
     *
     * @var BookingFactoryInterface
     */
    protected $bookingFactory;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param BookingFactoryInterface      $bookingFactory     The booking factory.
     * @param wpdb                         $wpdb               The WPDB instance to use to prepare and execute queries.
     * @param TemplateInterface            $expressionTemplate The template for rendering SQL expressions.
     * @param array|stdClass|Traversable   $tables             The tables names (values) mapping to their aliases (keys)
     *                                                         or null for no aliasing.
     * @param string[]|Stringable[]        $fieldColumnMap     A map of field names to table column names.
     * @param LogicalExpressionInterface[] $joins              A list of JOIN expressions to use in SELECT queries.
     */
    public function __construct(
        BookingFactoryInterface $bookingFactory,
        wpdb $wpdb,
        TemplateInterface $expressionTemplate,
        $tables,
        $fieldColumnMap,
        $joins = []
    ) {
        $this->_init($wpdb, $expressionTemplate, $tables, $fieldColumnMap, $joins);
        $this->bookingFactory = $bookingFactory;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function select(
        LogicalExpressionInterface $condition = null,
        $ordering = null,
        $limit = null,
        $offset = null
    ) {
        $records  = parent::select($condition, $ordering, $limit, $offset);
        $bookings = [];

        foreach ($records as $_record) {
            $bookings[] = $this->bookingFactory->make($_record);
        }

        return $bookings;
    }
}
