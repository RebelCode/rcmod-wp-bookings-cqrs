<?php

namespace RebelCode\Storage\Resource\WordPress\Module;

use Dhii\Event\EventFactoryInterface;
use Dhii\Invocation\InvocableInterface;
use Dhii\Util\Normalization\NormalizeStringCapableTrait;
use Dhii\Util\String\StringableInterface as Stringable;
use Exception;
use Psr\EventManager\EventManagerInterface;
use RebelCode\Modular\Events\EventsConsumerTrait;

/**
 * The handler for invoking automatic database migrations.
 *
 * @since [*next-version*]
 */
class AutoMigrationsHandler implements InvocableInterface
{
    /* @since [*next-version*] */
    use EventsConsumerTrait;

    /* @since [*next-version*] */
    use NormalizeStringCapableTrait;

    /**
     * The migrator instance.
     *
     * @since [*next-version*]
     *
     * @var Migrator
     */
    protected $migrator;

    /**
     * The DB version to migrate to.
     *
     * @since [*next-version*]
     *
     * @var int|string|Stringable
     */
    protected $targetVersion;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param Migrator              $migrator      The migrator instance.
     * @param Stringable|int|string $targetVersion The target DB version to migrate to.
     * @param EventManagerInterface $eventManager  The event manager instance.
     * @param EventFactoryInterface $eventFactory  The event factory instance for creating events.
     */
    public function __construct(Migrator $migrator, $targetVersion, $eventManager, $eventFactory)
    {
        $this->_setMigrator($migrator);
        $this->_setTargetVersion($targetVersion);
        $this->_setEventManager($eventManager);
        $this->_setEventFactory($eventFactory);
    }

    /**
     * Retrieves the migrator instance.
     *
     * @since [*next-version*]
     *
     * @return Migrator The migrator instance.
     */
    protected function _getMigrator()
    {
        return $this->migrator;
    }

    /**
     * Sets the migrator instance.
     *
     * @since [*next-version*]
     *
     * @param Migrator $migrator The migrator instance.
     */
    protected function _setMigrator(Migrator $migrator)
    {
        $this->migrator = $migrator;
    }

    /**
     * Retrieves the target DB migration version.
     *
     * @since [*next-version*]
     *
     * @return int|string|Stringable The target DB migration version.
     */
    protected function _getTargetVersion()
    {
        return $this->targetVersion;
    }

    /**
     * Retrieves the target DB migration version.
     *
     * @since [*next-version*]
     *
     * @param int|string|Stringable $targetVersion The target DB migration version.
     */
    protected function _setTargetVersion($targetVersion)
    {
        $this->targetVersion = $this->_normalizeString($targetVersion);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function __invoke()
    {
        $target   = $this->_getTargetVersion();
        $migrator = $this->_getMigrator();

        try {
            // Trigger "before" event
            $this->_trigger('wp_bookings_cqrs_before_migration', ['target' => $target]);

            // Migrate
            $migrator->migrate($target);

            // Trigger "after" event
            $this->_trigger('wp_bookings_cqrs_after_migration', ['target' => $target]);
        } catch (Exception $exception) {
            $this->_trigger('wp_bookings_cqrs_on_migration_failed', ['target' => $target]);
        }
    }
}
