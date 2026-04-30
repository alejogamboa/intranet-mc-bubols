<?php
/**
 * Plugin Name: MC Intranet Core
 * Plugin URI:  https://example.com/mc-intranet-core
 * Description: Plugin principal de lógica de negocio para MC Intranet. Registra CPTs, taxonomías, shortcodes y roles. Todo lo que NO es presentación va aquí.
 * Version:     1.0.0
 * Requires at least: 6.0
 * Requires PHP: 8.1
 * Author:      MC Group
 * Text Domain: mc-intranet-core
 * Domain Path: /languages
 *
 * @package MC_Intranet_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ─── Constantes ──────────────────────────────────────────────────────────────

define( 'MC_CORE_VERSION',   '1.0.0' );
define( 'MC_CORE_DIR',       plugin_dir_path( __FILE__ ) );
define( 'MC_CORE_URL',       plugin_dir_url( __FILE__ ) );
define( 'MC_CORE_TEMPLATES', MC_CORE_DIR . 'templates/' );

// ─── Autoload de clases ───────────────────────────────────────────────────────

require_once MC_CORE_DIR . 'includes/class-plugin.php';
require_once MC_CORE_DIR . 'includes/class-post-types.php';
require_once MC_CORE_DIR . 'includes/class-taxonomies.php';
require_once MC_CORE_DIR . 'includes/class-shortcodes.php';
require_once MC_CORE_DIR . 'includes/class-access-control.php';
require_once MC_CORE_DIR . 'includes/class-meta-boxes.php';
require_once MC_CORE_DIR . 'includes/class-company-context.php';
require_once MC_CORE_DIR . 'includes/class-directorio-importer.php';

// ─── Hooks de activación / desactivación ─────────────────────────────────────

register_activation_hook( __FILE__, [ 'MC_Intranet_Plugin', 'activate' ] );
register_deactivation_hook( __FILE__, [ 'MC_Intranet_Plugin', 'deactivate' ] );

// ─── Arranque ────────────────────────────────────────────────────────────────

MC_Intranet_Plugin::get_instance();
