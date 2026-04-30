<?php
/**
 * Helper de contexto de empresa — MC Intranet Core.
 *
 * Agrega body_class y genera el atributo data-company según
 * el post_meta company_context de la página actual.
 *
 * @package MC_Intranet_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MC_Intranet_Company_Context {

    /** Valores permitidos para company_context */
    const ALLOWED = [ 'mc', 'anstra', 'essenza', 'budefry', 'interactua' ];

    public function __construct() {
        // El tema ya aplica el data-company vía mc_get_company_context().
        // Este módulo puede extender la lógica si el tema no está activo.
        add_filter( 'body_class', [ $this, 'add_body_class' ] );
    }

    /**
     * Agrega clases CSS de empresa al body cuando el tema no lo hace.
     * Si el tema mc-intranet ya aplica estas clases no se duplican (WordPress
     * no agrega clases duplicadas en body_class).
     */
    public function add_body_class( array $classes ): array {
        if ( ! is_singular() ) {
            return $classes;
        }
        $context = get_post_meta( get_the_ID(), 'company_context', true );
        if ( $context && in_array( $context, self::ALLOWED, true ) ) {
            $classes[] = 'has-company-context';
            $classes[] = 'company--' . sanitize_html_class( $context );
        }
        return $classes;
    }

    /**
     * Retorna el company_context validado del post actual.
     *
     * @param  int|null $post_id
     * @return string  Valor válido o 'default'.
     */
    public static function get_context( ?int $post_id = null ): string {
        $id      = $post_id ?? get_the_ID();
        $context = (string) get_post_meta( $id, 'company_context', true );
        return in_array( $context, self::ALLOWED, true ) ? $context : 'default';
    }
}
