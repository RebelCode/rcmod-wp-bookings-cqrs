<?php

namespace RebelCode\Storage\Resource\WordPress\Module;

use Dhii\Config\ConfigFactoryInterface;
use Dhii\Data\Container\ContainerFactoryInterface;
use Dhii\Event\EventFactoryInterface;
use Dhii\Exception\InternalException;
use Dhii\Util\Normalization\NormalizeArrayCapableTrait;
use Dhii\Util\String\StringableInterface as Stringable;
use Psr\Container\ContainerInterface;
use Psr\EventManager\EventInterface;
use Psr\EventManager\EventManagerInterface;
use RebelCode\Modular\Module\AbstractBaseModule;

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
            $this->_loadPhpConfigFile(RC_WP_BOOKINGS_CQRS_MODULE_SERVICES_FILE)
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
