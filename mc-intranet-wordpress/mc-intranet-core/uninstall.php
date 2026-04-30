<?php
/**
 * Desinstalación del plugin MC Intranet Core.
 *
 * Se ejecuta solo cuando el administrador hace "Borrar" en el panel de plugins
 * (no en desactivar). Limpia datos propios del plugin.
 *
 * @package MC_Intranet_Core
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Eliminar opciones del plugin registradas en Settings API
$options = [
    'mc_intranet_site_name',
    'mc_intranet_support_url',
    'mc_intranet_cta_default_label',
];

foreach ( $options as $option ) {
    delete_option( $option );
}

// Nota: los datos de CPT (mc_formulario, mc_evento, mc_reconocimiento, mc_sede)
// se conservan deliberadamente para proteger el contenido editorial.
// Si se desea borrarlos, descomentar el siguiente bloque:
/*
$post_types = [ 'mc_formulario', 'mc_evento', 'mc_reconocimiento', 'mc_sede' ];
foreach ( $post_types as $pt ) {
    $posts = get_posts( [ 'post_type' => $pt, 'numberposts' => -1, 'post_status' => 'any' ] );
    foreach ( $posts as $post ) {
        wp_delete_post( $post->ID, true );
    }
}
*/
