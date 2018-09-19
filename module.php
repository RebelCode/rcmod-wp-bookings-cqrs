<?php

use Psr\Container\ContainerInterface;
use RebelCode\Storage\Resource\WordPress\Module\WpBookingsCqrsModule;

return function (ContainerInterface $c) {
    return new WpBookingsCqrsModule(
        [
            'key'                => 'wp_bookings_cqrs',
            'dependencies'       => ['wp_cqrs'],
            'config_file_path'   => __DIR__ . '/config.php',
            'services_file_path' => __DIR__ . '/services.php',
        ],
        $c->get('config_factory'),
        $c->get('container_factory'),
        $c->get('composite_container_factory'),
        $c->get('event_manager'),
        $c->get('event_factory')
    );
};
