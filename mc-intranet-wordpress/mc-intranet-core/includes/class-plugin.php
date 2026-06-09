<?php
/**
 * Clase principal del plugin — MC Intranet Core.
 * Patrón singleton. Registra los módulos y sus hooks.
 *
 * @package MC_Intranet_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MC_Intranet_Plugin {

    /** @var MC_Intranet_Plugin|null Instancia singleton */
    private static ?MC_Intranet_Plugin $instance = null;

    // ─── Singleton ───────────────────────────────────────────────────────────

    private function __construct() {
        $this->init_modules();
    }

    public static function get_instance(): MC_Intranet_Plugin {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // ─── Módulos ─────────────────────────────────────────────────────────────

    private function init_modules(): void {
        new MC_Intranet_Post_Types();
        new MC_Intranet_Taxonomies();
        new MC_Intranet_Shortcodes();
        new MC_Intranet_Access_Control();
        new MC_Intranet_Meta_Boxes();
        new MC_Intranet_Company_Context();
        new MC_Intranet_Directorio_Importer();
        new MC_Intranet_Branding_Settings();
        new MC_Intranet_User_Company();
        new MC_Intranet_User_Importer();
    }

    // ─── Activación / Desactivación ──────────────────────────────────────────

    public static function activate(): void {
        // Registrar CPTs y taxonomías temporalmente para que flush_rewrite_rules funcione.
        $pt = new MC_Intranet_Post_Types();
        $tax = new MC_Intranet_Taxonomies();
        $pt->register();
        $tax->register();
        MC_Intranet_Access_Control::activate();
        flush_rewrite_rules();
    }

    public static function deactivate(): void {
        flush_rewrite_rules();
    }
}
