<?php

namespace RebelCode\Storage\Resource\WordPress\Module;

use Psr\Container\ContainerInterface;
use Psr\EventManager\EventInterface;
use RebelCode\Modular\Module\AbstractBaseFileConfigModule;

/**
 * The WordPress Bookings CQRS Module.
 *
 * @since [*next-version*]
 */
class WpBookingsCqrsModule extends AbstractBaseFileConfigModule
{
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
