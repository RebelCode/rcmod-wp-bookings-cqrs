<?php

namespace RebelCode\Storage\Resource\WordPress\Wpdb;

use ArrayAccess;
use Dhii\Expression\LogicalExpressionInterface;
use Dhii\Output\TemplateInterface;
use Dhii\Storage\Resource\InsertCapableInterface;
use Dhii\Storage\Resource\SelectCapableInterface;
use Dhii\Util\String\StringableInterface as Stringable;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use stdClass;
use wpdb;

/**
 * A resource model for bookings stored in a custom table, with the status being stored in a separate, status log
 * table. Updating bookings with a status change involves inserting corresponding records for status logs.
 *
 * @since [*next-version*]
 */
class BookingWpdbUpdateResourceModel extends AbstractBaseWpdbUpdateResourceModel
{
    /**
     * The SELECT resource model for bookings.
     *
     * @since [*next-version*]
     *
     * @var SelectCapableInterface|null
     */
    protected $bookingsSelectRm;

    /**
     * The INSERT resource model for status logs.
     *
     * @since [*next-version*]
     *
     * @var InsertCapableInterface|null
     */
    protected $statusLogInsertRm;

    /**
     * The callback that is used to determine the user ID to insert for a status log record.
     *
     * @since [*next-version*]
     *
     * @var callable
     */
    protected $userIdCallback;

    /**
     * The expression builder.
     *
     * @since [*next-version*]
     *
     * @var object
     */
    protected $exprBuilder;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param wpdb                   $wpdb               The WPDB instance to use to prepare and execute queries.
     * @param TemplateInterface      $expressionTemplate The template for rendering SQL expressions.
     * @param string|Stringable      $table              The table to insert records into.
     * @param string[]|Stringable[]  $fieldColumnMap     A map of field names to table column names.
     * @param SelectCapableInterface $statusLogInsertRm  The INSERT resource model for status logs, if any.
     * @param callable               $userIdCallback     A callback that returns the user ID to insert for a status log
     *                                                   record. The callback will receive the corresponding booking
     *                                                   that is being inserted, as argument.
     */
    public function __construct(
        wpdb $wpdb,
        TemplateInterface $expressionTemplate,
        $table,
        $fieldColumnMap,
        $statusLogInsertRm,
        $userIdCallback
    ) {
        $this->_init($wpdb, $expressionTemplate, $table, $fieldColumnMap);
        $this->statusLogInsertRm = $statusLogInsertRm;
        $this->userIdCallback    = $userIdCallback;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function update($changeSet, LogicalExpressionInterface $condition = null, $ordering = null, $limit = null)
    {
        parent::update($changeSet, $condition, $ordering, $limit);

        try {
            $status = $this->_containerGet($changeSet, 'status');

            // Re-query to get the updated bookings
            $bookings = $this->bookingsSelectRm->select($condition, $ordering, $limit);

            // Add a status log entry for each
            foreach ($bookings as $_booking) {
                $_bookingId = $this->_containerGet($_booking, 'id');
                $_userId    = $this->_invokeUserIdCallback($_booking);
                $this->statusLogInsertRm->insert([
                    [
                        'booking_id'     => $_bookingId,
                        'booking_status' => $status,
                        'user_id'        => $_userId,
                    ],
                ]);
            }
        } catch (NotFoundExceptionInterface $notFoundException) {
            return;
        }
    }

    /**
     * Invokes the user ID callback to obtain the ID of the current user.
     *
     * @since [*next-version*]
     *
     * @param array|ArrayAccess|stdClass|ContainerInterface $booking The booking that is being inserted.
     *
     * @return int|string The user ID.
     */
    protected function _invokeUserIdCallback($booking)
    {
        return call_user_func_array($this->userIdCallback, [$booking]);
    }
}
