<?php

/**
 * Versioned wp-config.php for local Docker development.
 *
 * Values come from container environment variables set in docker-compose.yml.
 */

define('DB_NAME', getenv('WORDPRESS_DB_NAME') ?: 'wordpress_xxxx');
define('DB_USER', getenv('WORDPRESS_DB_USER') ?: 'wordpress');
define('DB_PASSWORD', getenv('WORDPRESS_DB_PASSWORD') ?: 'wordpress');
define('DB_HOST', getenv('WORDPRESS_DB_HOST') ?: 'db');
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');

define('AUTH_KEY',         'change-this-auth-key');
define('SECURE_AUTH_KEY',  'change-this-secure-auth-key');
define('LOGGED_IN_KEY',    'change-this-logged-in-key');
define('NONCE_KEY',        'change-this-nonce-key');
define('AUTH_SALT',        'change-this-auth-salt');
define('SECURE_AUTH_SALT', 'change-this-secure-auth-salt');
define('LOGGED_IN_SALT',   'change-this-logged-in-salt');
define('NONCE_SALT',       'change-this-nonce-salt');

define('WP_ENVIRONMENT_TYPE', 'local');

$table_prefix = 'wp_';

define('WP_DEBUG', true);
define('WP_DEBUG_LOG', false);
define('WP_DEBUG_DISPLAY', false);

if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/../');
}

require_once ABSPATH . 'wp-settings.php';
