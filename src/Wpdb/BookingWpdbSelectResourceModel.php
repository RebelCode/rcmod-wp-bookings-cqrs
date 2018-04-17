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
     * The SELECT resource model for status logs.
     *
     * @since [*next-version*]
     *
     * @var SelectCapableInterface|null
     */
    protected $statusLogRm;

    /**
     * The ordering information for status log SELECTing.
     *
     * @since [*next-version*]
     *
     * @var OrderInterface[]
     */
    protected $statusLogOrdering;

    /**
     * The expression builder.
     *
     * @since [*next-version*]
     *
     * @var object
     */
    protected $exprBuilder;

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
     * @param SelectCapableInterface       $statusLogRm        The SELECT resource model for status logs.
     * @param FactoryInterface             $orderFactory       The factory for creating {@see OrderInterface} instances,
     *                                                         if any.
     * @param object                       $exprBuilder        The expression builder, if any.
     * @param LogicalExpressionInterface[] $joins              A list of JOIN expressions to use in SELECT queries.
     */
    public function __construct(
        BookingFactoryInterface $bookingFactory,
        wpdb $wpdb,
        TemplateInterface $expressionTemplate,
        $tables,
        $fieldColumnMap,
        $statusLogRm,
        $orderFactory,
        $exprBuilder,
        $joins = []
    ) {
        $this->_init($wpdb, $expressionTemplate, $tables, $fieldColumnMap, $joins);
        $this->bookingFactory = $bookingFactory;
        $this->statusLogRm = $statusLogRm;
        $this->exprBuilder = $exprBuilder;
        $this->statusLogOrdering = [
            $orderFactory->make(['field' => 'date', 'ascending' => false]),
            $orderFactory->make(['field' => 'id', 'ascending' => false]),
        ];
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
        $records = parent::select($condition, $ordering, $limit, $offset);
        $bookings = [];

        foreach ($records as $_record) {
            if (!$this->_containerHas($_record, 'id')) {
                continue;
            }
            $id = $this->_containerGet($_record, 'id');
            $status = $this->_getBookingStatus($id);

            $this->_containerSet($_record, 'status', $status);

            $bookings[] = $this->bookingFactory->make($_record);
        }

        return $bookings;
    }

    /**
     * Retrieves the status for a booking.
     *
     * @since [*next-version*]
     *
     * @param int|string|Stringable $id The booking's ID.
     *
     * @return string|Stringable The booking's status, if any.
     */
    protected function _getBookingStatus($id)
    {
        $statusLogs = $this->statusLogRm->select(
            $this->exprBuilder->eq(
                $this->exprBuilder->var('booking_id'),
                $this->exprBuilder->lit($id)
            ),
            $this->statusLogOrdering,
            1
        );
        $statusLog = $this->_containerGet($statusLogs, 0);

        return $this->_containerHas($statusLog, 'name')
            ? $this->_containerGet($statusLog, 'name')
            : BookingStatusInterface::STATUS_NONE;
    }
}
