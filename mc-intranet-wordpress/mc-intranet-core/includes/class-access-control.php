<?php
/**
 * Control de acceso frontend para MC Intranet.
 *
 * @package MC_Intranet_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MC_Intranet_Access_Control {

    public const ACCESS_CAPABILITY      = 'access_mc_intranet';
    private const LOGIN_PAGE_SLUG       = 'ingresar';
    private const DENIED_PAGE_SLUG      = 'acceso-denegado';
    private const LOGIN_PAGE_OPTION     = 'mc_intranet_login_page_id';
    private const DENIED_PAGE_OPTION    = 'mc_intranet_denied_page_id';

    public function __construct() {
        add_action( 'init', [ $this, 'maybe_bootstrap_system_pages' ] );
        add_action( 'template_redirect', [ $this, 'protect_frontend_pages' ], 1 );
        add_filter( 'login_url', [ $this, 'filter_login_url' ], 10, 3 );
        add_action( 'wp_login_failed', [ $this, 'handle_login_failed' ] );
    }

    public static function activate(): void {
        self::grant_access_capability();
        self::ensure_system_pages();
    }

    public function maybe_bootstrap_system_pages(): void {
        if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
            return;
        }

        if ( ! self::get_login_page_id() || ! self::get_denied_page_id() ) {
            self::grant_access_capability();
            self::ensure_system_pages();
        }
    }

    public function protect_frontend_pages(): void {
        if ( is_admin() || wp_doing_ajax() || wp_doing_cron() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
            return;
        }

        if ( $this->is_public_system_page() ) {
            $this->maybe_redirect_authenticated_user();
            return;
        }

        if ( ! is_user_logged_in() ) {
            wp_safe_redirect( self::get_login_page_url( self::get_requested_redirect_target() ) );
            exit;
        }

        if ( ! current_user_can( self::ACCESS_CAPABILITY ) ) {
            wp_safe_redirect( self::get_denied_page_url() );
            exit;
        }
    }

    public function filter_login_url( string $login_url, string $redirect, bool $force_reauth ): string {
        if ( $force_reauth ) {
            return $login_url;
        }

        // Do NOT call get_login_page_url() here — it falls back to wp_login_url() which fires
        // this same filter again, causing infinite recursion and memory exhaustion.
        $page_id = self::get_login_page_id();
        if ( ! $page_id ) {
            return $login_url;
        }

        $url = get_permalink( $page_id );
        if ( ! $url ) {
            return $login_url;
        }

        if ( $redirect ) {
            return add_query_arg( 'redirect_to', rawurlencode( $redirect ), $url );
        }

        return $url;
    }

    public function handle_login_failed( string $username ): void {
        unset( $username );

        $referrer       = wp_get_referer();
        $login_page_url = self::get_login_page_url();

        if ( ! $referrer || 0 !== strpos( $referrer, $login_page_url ) ) {
            return;
        }

        $redirect_to = self::get_requested_redirect_target();
        $target_url  = add_query_arg( 'mc_login', 'failed', $login_page_url );

        if ( $redirect_to ) {
            $target_url = add_query_arg( 'redirect_to', $redirect_to, $target_url );
        }

        wp_safe_redirect( $target_url );
        exit;
    }

    public static function get_login_page_url( string $redirect_to = '' ): string {
        $page_id = self::get_login_page_id();
        $url     = $page_id ? get_permalink( $page_id ) : wp_login_url();

        if ( ! $url ) {
            $url = wp_login_url();
        }

        if ( $redirect_to ) {
            return add_query_arg( 'redirect_to', $redirect_to, $url );
        }

        return $url;
    }

    public static function get_denied_page_url(): string {
        $page_id = self::get_denied_page_id();
        $url     = $page_id ? get_permalink( $page_id ) : home_url( '/' );

        return $url ?: home_url( '/' );
    }

    public static function get_requested_redirect_target(): string {
        $redirect_to = isset( $_REQUEST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_REQUEST['redirect_to'] ) ) : '';

        if ( $redirect_to ) {
            return self::normalize_redirect_target( $redirect_to );
        }

        $request_uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '/';

        return self::normalize_redirect_target( home_url( $request_uri ) );
    }

    private function maybe_redirect_authenticated_user(): void {
        if ( ! is_user_logged_in() ) {
            return;
        }

        if ( $this->is_login_page() ) {
            if ( current_user_can( self::ACCESS_CAPABILITY ) ) {
                wp_safe_redirect( self::get_requested_redirect_target() ?: home_url( '/' ) );
                exit;
            }

            wp_safe_redirect( self::get_denied_page_url() );
            exit;
        }

        if ( $this->is_denied_page() && current_user_can( self::ACCESS_CAPABILITY ) ) {
            wp_safe_redirect( home_url( '/' ) );
            exit;
        }
    }

    private function is_public_system_page(): bool {
        return $this->is_login_page() || $this->is_denied_page();
    }

    private function is_login_page(): bool {
        $page_id = self::get_login_page_id();

        return $page_id && is_page( $page_id );
    }

    private function is_denied_page(): bool {
        $page_id = self::get_denied_page_id();

        return $page_id && is_page( $page_id );
    }

    private static function get_login_page_id(): int {
        return absint( get_option( self::LOGIN_PAGE_OPTION, 0 ) );
    }

    private static function get_denied_page_id(): int {
        return absint( get_option( self::DENIED_PAGE_OPTION, 0 ) );
    }

    private static function normalize_redirect_target( string $target ): string {
        if ( ! $target ) {
            return home_url( '/' );
        }

        // Use get_permalink() directly to avoid calling wp_login_url() which fires the
        // login_url filter and causes infinite recursion when no custom page is set yet.
        $login_id  = self::get_login_page_id();
        $denied_id = self::get_denied_page_id();

        if ( $login_id ) {
            $login_url = get_permalink( $login_id );
            if ( $login_url && 0 === strpos( $target, $login_url ) ) {
                return home_url( '/' );
            }
        }

        if ( $denied_id ) {
            $denied_url = get_permalink( $denied_id );
            if ( $denied_url && 0 === strpos( $target, $denied_url ) ) {
                return home_url( '/' );
            }
        }

        return $target;
    }

    private static function grant_access_capability(): void {
        foreach ( [ 'subscriber', 'editor', 'administrator' ] as $role_name ) {
            $role = get_role( $role_name );

            if ( $role instanceof WP_Role ) {
                $role->add_cap( self::ACCESS_CAPABILITY );
            }
        }
    }

    private static function ensure_system_pages(): void {
        self::ensure_system_page(
            self::LOGIN_PAGE_SLUG,
            __( 'Ingreso Intranet', 'mc-intranet-core' ),
            self::LOGIN_PAGE_OPTION,
            '[mc_login_screen]'
        );

        self::ensure_system_page(
            self::DENIED_PAGE_SLUG,
            __( 'Acceso denegado', 'mc-intranet-core' ),
            self::DENIED_PAGE_OPTION,
            '[mc_access_denied]'
        );
    }

    private static function ensure_system_page( string $slug, string $title, string $option_name, string $shortcode ): void {
        $page = get_page_by_path( $slug );

        if ( ! $page instanceof WP_Post ) {
            $page_id = wp_insert_post( [
                'post_title'   => $title,
                'post_name'    => $slug,
                'post_type'    => 'page',
                'post_status'  => 'publish',
                'post_content' => $shortcode,
            ] );

            if ( ! is_wp_error( $page_id ) ) {
                update_option( $option_name, (int) $page_id );
            }

            return;
        }

        update_option( $option_name, (int) $page->ID );
    }
}