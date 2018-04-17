<?php

namespace RebelCode\Storage\Resource\WordPress\Wpdb;

use ArrayAccess;
use Dhii\Data\Container\ContainerHasCapableTrait;
use Dhii\Data\Container\ContainerSetCapableTrait;
use Dhii\Storage\Resource\InsertCapableInterface;
use Dhii\Storage\Resource\SelectCapableInterface;
use Dhii\Util\String\StringableInterface as Stringable;
use Psr\Container\ContainerInterface;
use stdClass;
use wpdb;

/**
 * A resource model for bookings stored in a custom table, with the status being stored in a separate, status log
 * table. Inserting bookings involves inserting corresponding records for status logs.
 *
 * @since [*next-version*]
 */
class BookingWpdbInsertResourceModel extends AbstractBaseWpdbInsertResourceModel
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
     * The INSERT resource model for status logs.
     *
     * @since [*next-version*]
     *
     * @var InsertCapableInterface|null
     */
    protected $statusLogRm;

    /**
     * The callback that is used to determine the user ID to insert for a status log record.
     *
     * @since [*next-version*]
     *
     * @var callable
     */
    protected $userIdCallback;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param wpdb                   $wpdb           The WPDB instance to use to prepare and execute queries.
     * @param string|Stringable      $table          The table to insert records into.
     * @param string[]|Stringable[]  $fieldColumnMap A map of field names to table column names.
     * @param bool                   $insertBulk     True to insert records in a single bulk query, false to
     *                                               insert them in separate queries.
     * @param SelectCapableInterface $statusLogRm    The INSERT resource model for status logs, if any.
     * @param callable               $userIdCallback A callback that returns the user ID to insert for a status log
     *                                               record. The callback will receive the corresponding booking that is
     *                                               being inserted, as argument.
     */
    public function __construct(
        wpdb $wpdb,
        $table,
        $fieldColumnMap,
        $insertBulk,
        $statusLogRm,
        $userIdCallback
    ) {
        $this->_init($wpdb, $table, $fieldColumnMap, $insertBulk);
        $this->statusLogRm = $statusLogRm;
        $this->userIdCallback = $userIdCallback;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function insert($records)
    {
        parent::insert($records);

        if ($this->statusLogRm === null) {
            return;
        }

        foreach ($records as $_booking) {
            $_bookingId = $this->_containerGet($_booking, 'id');
            $_bookingStatus = $this->_containerGet($_booking, 'status');
            $userId = $this->_invokeUserIdCallback($_booking);

            $this->statusLogRm->insert([
                [
                    'booking_id'     => $_bookingId,
                    'booking_status' => $_bookingStatus,
                    'user_id'        => $userId,
                ],
            ]);
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
