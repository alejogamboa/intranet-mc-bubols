<?php
/**
 * Registro de taxonomías — MC Intranet Core.
 *
 * Taxonomías:
 *  - mc_empresa  → mc, anstra, essenza, budefry, interactua
 *  - mc_area     → administracion, tic, gestiones, rrhh, cultura
 *
 * @package MC_Intranet_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MC_Intranet_Taxonomies {

    public function __construct() {
        add_action( 'init', [ $this, 'register' ] );
    }

    public function register(): void {
        $this->register_empresa();
        $this->register_area();
    }

    private function register_empresa(): void {
        register_taxonomy( 'mc_empresa', [ 'mc_formulario', 'mc_evento', 'mc_reconocimiento' ], [
            'labels'       => [
                'name'          => __( 'Empresas', 'mc-intranet-core' ),
                'singular_name' => __( 'Empresa', 'mc-intranet-core' ),
            ],
            'public'       => false,
            'show_ui'      => true,
            'show_in_rest' => true,
            'hierarchical' => false,
            'rewrite'      => false,
        ] );
    }

    private function register_area(): void {
        register_taxonomy( 'mc_area', [ 'mc_formulario' ], [
            'labels'       => [
                'name'          => __( 'Áreas', 'mc-intranet-core' ),
                'singular_name' => __( 'Área', 'mc-intranet-core' ),
            ],
            'public'       => false,
            'show_ui'      => true,
            'show_in_rest' => true,
            'hierarchical' => false,
            'rewrite'      => false,
        ] );
    }
}
