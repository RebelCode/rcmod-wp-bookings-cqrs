<?php

namespace RebelCode\Storage\Resource\WordPress;

use ArrayAccess;
use DateTime;
use DateTimeZone;
use Dhii\Data\Container\ContainerGetCapableTrait;
use Dhii\Data\Container\ContainerGetPathCapableTrait;
use Dhii\Data\Container\ContainerHasCapableTrait;
use Dhii\Data\Container\ContainerSetCapableTrait;
use Dhii\Data\Container\NormalizeKeyCapableTrait;
use Dhii\Exception\CreateOutOfRangeExceptionCapableTrait;
use Dhii\Expression\LogicalExpressionInterface;
use Dhii\Factory\FactoryInterface;
use Dhii\Storage\Resource\DeleteCapableInterface;
use Dhii\Storage\Resource\InsertCapableInterface;
use Dhii\Storage\Resource\SelectCapableInterface;
use Dhii\Storage\Resource\UpdateCapableInterface;
use Dhii\Util\Normalization\NormalizeIterableCapableTrait;
use Dhii\Util\Normalization\NormalizeStringCapableTrait;
use Dhii\Util\String\StringableInterface as Stringable;
use Exception;
use InvalidArgumentException;
use OutOfRangeException;
use Psr\Container\ContainerInterface;
use stdClass;
use Traversable;

/**
 * An entity manager implementation specific for resource and their session rules.
 *
 * @since [*next-version*]
 */
class ResourcesEntityManager extends BaseCqrsEntityManager
{
    /* @since [*next-version*] */
    use ContainerGetPathCapableTrait;

    /* @since [*next-version*] */
    use ContainerGetCapableTrait;

    /* @since [*next-version*] */
    use ContainerHasCapableTrait;

    /* @since [*next-version*] */
    use ContainerSetCapableTrait;

    /* @since [*next-version*] */
    use NormalizeKeyCapableTrait;

    /* @since [*next-version*] */
    use NormalizeStringCapableTrait;

    /* @since [*next-version*] */
    use NormalizeIterableCapableTrait;

    /* @since [*next-version*] */
    use CreateOutOfRangeExceptionCapableTrait;

    /**
     * The key in resource entities where availability rules are stored.
     *
     * May be a path, delimited by forward slashes.
     *
     * @since [*next-version*]
     */
    const K_ENTITY_SESSION_RULES = 'availability/rules';

    /**
     * The key in resource DB records where the timezone is stored.
     *
     * May be a path, delimited by forward slashes.
     *
     * @since [*next-version*]
     */
    const K_RECORD_TIMEZONE = 'timezone';

    /**
     * The key in resource entities where the timezone is stored.
     *
     * May be a path, delimited by forward slashes.
     *
     * @since [*next-version*]
     */
    const K_ENTITY_TIMEZONE = 'availability/timezone';

    /**
     * The key in resource DB records where the image ID is stored.
     *
     * May be a path, delimited by forward slashes.
     *
     * @since [*next-version*]
     */
    const K_RECORD_IMAGE_ID = 'data/imageId';

    /**
     * The key in resource entities where the image URL is stored.
     *
     * May be a path, delimited by forward slashes.
     *
     * @since [*next-version*]
     */
    const K_ENTITY_IMAGE_URL = 'data/imageUrl';

    /**
     * The session rules SELECT resource model.
     *
     * @since [*next-version*]
     *
     * @var SelectCapableInterface
     */
    protected $rulesSelectRm;

    /**
     * The session rules INSERT resource model.
     *
     * @since [*next-version*]
     *
     * @var InsertCapableInterface
     */
    protected $rulesInsertRm;

    /**
     * The session rules UPDATE resource model.
     *
     * @since [*next-version*]
     *
     * @var UpdateCapableInterface
     */
    protected $rulesUpdateRm;

    /**
     * The session rules DELETE resource model.
     *
     * @since [*next-version*]
     *
     * @var DeleteCapableInterface
     */
    protected $rulesDeleteRm;

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     *
     * @param SelectCapableInterface $rulesSelectRm The session rules SELECT resource model.
     * @param InsertCapableInterface $rulesInsertRm The session rules INSERT resource model.
     * @param UpdateCapableInterface $rulesUpdateRm The session rules UPDATE resource model.
     * @param DeleteCapableInterface $rulesDeleteRm The session rules DELETE resource model.
     */
    public function __construct(
        SelectCapableInterface $selectRm,
        InsertCapableInterface $insertRm,
        UpdateCapableInterface $updateRm,
        DeleteCapableInterface $deleteRm,
        SelectCapableInterface $rulesSelectRm,
        InsertCapableInterface $rulesInsertRm,
        UpdateCapableInterface $rulesUpdateRm,
        DeleteCapableInterface $rulesDeleteRm,
        FactoryInterface $orderFactory,
        $exprBuilder
    ) {
        parent::__construct($selectRm, $insertRm, $updateRm, $deleteRm, $orderFactory, $exprBuilder);

        $this->rulesSelectRm = $rulesSelectRm;
        $this->rulesInsertRm = $rulesInsertRm;
        $this->rulesUpdateRm = $rulesUpdateRm;
        $this->rulesDeleteRm = $rulesDeleteRm;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function add($entity)
    {
        $id = parent::add($entity);

        // Update the session rules
        $this->_updateSessionRules($id, $entity);

        return $id;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function delete($id)
    {
        parent::delete($id);

        // Delete the session rules
        $this->rulesDeleteRm->delete($this->_createResourceIdExpression($id));
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function set($id, $entity)
    {
        $this->update($id, $entity);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function update($id, $data)
    {
        $this->updateRm->update($this->_entityToRecord($data), $this->_createIdExpression($id), null, 1);

        // Re-fetch the updated entity to get the full data
        $entity = $this->get($id);
        // Update the session rules
        $this->_updateSessionRules($id, $entity);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _recordToEntity($record)
    {
        $resource = $this->_normalizeArray($record);

        // Move timezone according to defined path
        if (isset($resource['timezone'])) {
            $this->_arraySetPath($resource, $this->_getTimezonePath(), $resource['timezone']);
        }

        // Retrieve rules for resource
        $rules = $this->rulesSelectRm->select($this->_createResourceIdExpression($resource['id']));
        // Store rules in resource according to path
        $this->_arraySetPath($resource, $this->_getSessionRulesPath(), $rules);

        // Unserialize the data if present in resource
        if (isset($resource['data']) && is_string($resource['data'])) {
            $resource['data'] = unserialize($resource['data']);
        }

        // Get image ID from record
        $imageId = $this->_arrayGetPath($resource, $this->_getImageIdPath(), null);
        // If found, set image URL in entity
        if ($imageId !== null) {
            $this->_arraySetPath($resource, $this->_getImageUrlPath(), $this->_wpGetImageUrl($imageId));
        }

        return $resource;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _entityToRecord($entity)
    {
        $entity = $this->_normalizeArray($entity);

        $this->_arrayUnsetPath($entity, $this->_getSessionRulesPath());
        $this->_arrayUnsetPath($entity, $this->_getImageUrlPath());

        return $entity;
    }

    /**
     * Retrieves the path for the session rules.
     *
     * @since [*next-version*]
     *
     * @return string[]|Stringable[] An array of path segments.
     */
    protected function _getSessionRulesPath()
    {
        return explode('/', static::K_ENTITY_SESSION_RULES);
    }

    /**
     * Retrieves the path for the timezone.
     *
     * @since [*next-version*]
     *
     * @return string[]|Stringable[] An array of path segments.
     */
    protected function _getTimezonePath()
    {
        return explode('/', static::K_ENTITY_TIMEZONE);
    }

    /**
     * Retrieves the DB record path for the image ID.
     *
     * @since [*next-version*]
     *
     * @return string[]|Stringable[] An array of path segments.
     */
    protected function _getImageIdPath()
    {
        return explode('/', static::K_RECORD_IMAGE_ID);
    }

    /**
     * Retrieves the entity path for the image URL.
     *
     * @since [*next-version*]
     *
     * @return string[]|Stringable[] An array of path segments.
     */
    protected function _getImageUrlPath()
    {
        return explode('/', static::K_ENTITY_IMAGE_URL);
    }

    /**
     * Creates an expression for matching an entity by its resource ID.
     *
     * @since [*next-version*]
     *
     * @param int|string|Stringable $id The resource ID.
     *
     * @return LogicalExpressionInterface The expression.
     */
    protected function _createResourceIdExpression($id)
    {
        return $this->exprBuilder->eq(
            $this->exprBuilder->var('resource_id'),
            $this->exprBuilder->lit($id)
        );
    }

    /**
     * Updates the session rules for the resource.
     *
     * @since [*next-version*]
     *
     * @param int|string|Stringable                         $id   The ID of the service.
     * @param array|stdClass|ArrayAccess|ContainerInterface $data The resource entity data..
     */
    protected function _updateSessionRules($id, $data)
    {
        $b = $this->exprBuilder;

        $rules    = $this->_containerGetPath($data, $this->_getSessionRulesPath());
        $timezone = $this->_containerGetPath($data, $this->_getTimezonePath());

        $ruleIds = [];

        foreach ($rules as $_ruleData) {
            $_rule = $this->_processSessionRuleData($id, $_ruleData, $timezone);

            // If rule has an ID, update the existing rule
            if ($this->_containerHas($_rule, 'id')) {
                $_ruleId  = $this->_containerGet($_rule, 'id');
                $_ruleExp = $b->eq(
                    $b->var('id'),
                    $b->lit($_ruleId)
                );

                $this->rulesUpdateRm->update($_rule, $_ruleExp);
            } else {
                // If rule has no ID, insert as a new rule
                $_newRuleIds = $this->rulesInsertRm->insert([$_rule]);
                $_ruleId     = $_newRuleIds[0];
            }

            $ruleIds[] = $_ruleId;
        }

        // Expression for matching the rules by their resource ID
        $deleteExpr = $b->eq($b->var('resource_id'), $b->lit($id));

        // If rules were added/updated, ignore them in the condition
        if (count($ruleIds) > 0) {
            $deleteExpr = $b->and(
                $deleteExpr,
                $b->not(
                    $b->in(
                        $b->var('id'),
                        $b->set($ruleIds)
                    )
                )
            );
        }

        // Delete the sessions rules according to the above condition
        $this->rulesDeleteRm->delete($deleteExpr);
    }

    /**
     * Processes the session rule data that was received in the request.
     *
     * @since [*next-version*]
     *
     * @param int|string|Stringable      $resourceId The ID of the resource.
     * @param array|stdClass|Traversable $ruleData   The session rule data that was received.
     * @param string|Stringable          $serviceTz  The service timezone name.
     *
     * @return array|stdClass|ArrayAccess|ContainerInterface The processed session rule data.
     */
    protected function _processSessionRuleData($resourceId, $ruleData, $serviceTz)
    {
        $allDay = $this->_containerGet($ruleData, 'isAllDay');

        // Parse the service timezone name into a timezone object
        $timezoneName = $this->_normalizeString($serviceTz);
        $timezone     = empty($timezoneName) ? null : $this->_createDateTimeZone($timezoneName);

        // Get the start ISO 8601 string, parse it and normalize it to the beginning of the day if required
        $startIso8601  = $this->_containerGet($ruleData, 'start');
        $startDatetime = new DateTime($startIso8601, $timezone);

        // Get the end ISO 8601 string, parse it and normalize it to the end of the day if required
        $endIso8601  = $this->_containerGet($ruleData, 'end');
        $endDateTime = new DateTime($endIso8601, $timezone);

        $data = [
            'id'                  => $this->_containerHas($ruleData, 'id')
                ? $this->_containerGet($ruleData, 'id')
                : null,
            'resource_id'         => $resourceId,
            'start'               => $startDatetime->getTimestamp(),
            'end'                 => $endDateTime->getTimestamp(),
            'all_day'             => $allDay,
            'repeat'              => $this->_containerGet($ruleData, 'repeat'),
            'repeat_period'       => $this->_containerGet($ruleData, 'repeatPeriod'),
            'repeat_unit'         => $this->_containerGet($ruleData, 'repeatUnit'),
            'repeat_until'        => $this->_containerGet($ruleData, 'repeatUntil'),
            'repeat_until_period' => $this->_containerGet($ruleData, 'repeatUntilPeriod'),
            'repeat_until_date'   => strtotime($this->_containerGet($ruleData, 'repeatUntilDate')),
            'repeat_weekly_on'    => implode(',', $this->_containerGet($ruleData, 'repeatWeeklyOn')),
            'repeat_monthly_on'   => implode(',', $this->_containerGet($ruleData, 'repeatMonthlyOn')),
        ];

        $excludeDates = [];
        foreach ($this->_containerGet($ruleData, 'excludeDates') as $_excludeDate) {
            $excludeDates[] = $this->_processExcludeDate($_excludeDate, $timezone);
        }

        $data['exclude_dates'] = implode(',', $excludeDates);

        return $data;
    }

    /**
     * Processes an excluded date to transform it into a timestamp.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable $excludeDate The exclude date string, in ISO8601 format.
     * @param DateTimeZone      $timezone    The service timezone.
     *
     * @return int|false The timestamp.
     */
    protected function _processExcludeDate($excludeDate, $timezone)
    {
        $datetime  = new DateTime($this->_normalizeString($excludeDate), $timezone);
        $timestamp = $datetime->getTimestamp();

        return $timestamp;
    }

    /**
     * Creates a {@link DateTimeZone} object for a timezone, by name.
     *
     * @see   DateTimeZone
     * @since [*next-version*]
     *
     * @param string|Stringable $tzName The name of the timezone.
     *
     * @throws InvalidArgumentException If the timezone name is not a string or stringable object.
     * @throws OutOfRangeException      If the timezone name is invalid and does not represent a valid timezone.
     *
     * @return DateTimeZone The created {@link DateTimeZone} instance.
     */
    protected function _createDateTimeZone($tzName)
    {
        $argTz  = $tzName;
        $tzName = $this->_normalizeString($tzName);

        // If the timezone is a UTC offset timezone, transform into a valid DateTimeZone offset.
        // See http://php.net/manual/en/datetimezone.construct.php
        if (preg_match('/^UTC(\+|\-)(\d{1,2})(:?(\d{2}))?$/', $tzName, $matches) && count($matches) >= 2) {
            $sign    = $matches[1];
            $hours   = (int) $matches[2];
            $minutes = count($matches) >= 4 ? (int) $matches[4] : 0;
            $tzName  = sprintf('%s%02d%02d', $sign, $hours, $minutes);
        }

        try {
            return new DateTimeZone($tzName);
        } catch (Exception $exception) {
            throw $this->_createOutOfRangeException(
                $this->__('Invalid timezone name: "%1$s"', [$argTz]), null, $exception, $argTz
            );
        }
    }

    /**
     * Retrieves the URL for a WordPress image, by ID.
     *
     * @since [*next-version*]
     *
     * @param int|string|Stringable $id The ID of the image for which to retrieve the URL.
     *
     * @return false|string The URL or false if no image with the given ID was found.
     */
    protected function _wpGetImageUrl($id)
    {
        return wp_get_attachment_url($id);
    }

    /**
     * Utility method for retrieving a deep value from an array using a path.
     *
     * @since [*next-version*]
     *
     * @param array      $array   The array.
     * @param array      $path    The path.
     * @param mixed|null $default The default value to return if a value does not exist at the given path.
     *
     * @return mixed
     */
    protected function _arrayGetPath(&$array, $path, $default = null)
    {
        $head = array_shift($path);

        if ($head === null || !array_key_exists($head, $array)) {
            return $default;
        }

        return count($path) > 0
            ? $this->_arrayGetPath($array[$head], $path, $default)
            : $array[$head];
    }

    /**
     * Utility method for setting a deep value in an array using a path.
     *
     * @since [*next-version*]
     *
     * @param array $array The array.
     * @param array $path  The path.
     */
    protected function _arraySetPath(&$array, $path, $value)
    {
        $head = array_shift($path);

        if ($head === null) {
            return;
        }

        if (count($path) > 0) {
            $array[$head] = [];
            $this->_arraySetPath($array[$head], $path, $value);

            return;
        }

        $array[$head] = $value;
    }

    /**
     * Utility method for removing a deep value in an array using a path.
     *
     * @since [*next-version*]
     *
     * @param array $array The array.
     * @param array $path  The path.
     */
    protected function _arrayUnsetPath(&$array, $path)
    {
        $head = array_shift($path);

        if ($head === null || !isset($array[$head])) {
            return;
        }

        if (count($path) > 0) {
            $this->_arrayUnsetPath($array[$head], $path);

            return;
        }

        unset($array[$head]);
    }
}
