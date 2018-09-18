<?php

use Psr\Container\ContainerInterface;
use RebelCode\Storage\Resource\WordPress\Module\WpBookingsCqrsModule;

define('RC_WP_BOOKINGS_CQRS_MODULE_KEY', 'wp_bookings_cqrs');
define('RC_WP_BOOKINGS_CQRS_MODULE_DIR', __DIR__);
define('RC_WP_BOOKINGS_CQRS_MODULE_CONFIG_DIR', RC_WP_BOOKINGS_CQRS_MODULE_DIR . '/config');
define('RC_WP_BOOKINGS_CQRS_MODULE_CONFIG_FILE', RC_WP_BOOKINGS_CQRS_MODULE_CONFIG_DIR . '/config.php');
define('RC_WP_BOOKINGS_CQRS_MODULE_SERVICES_FILE', RC_WP_BOOKINGS_CQRS_MODULE_CONFIG_DIR . '/services.php');
define('RC_WP_BOOKINGS_CQRS_MIGRATIONS_DIR', RC_WP_BOOKINGS_CQRS_MODULE_DIR . '/migrations');

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
