<?php
/**
 * Limpieza de mc_sede duplicados.
 *
 * Uso: wp eval-file .../cleanup-duplicate-sedes.php --allow-root
 *
 * Agrupa sedes por (empresa_slug|sanitize_title(post_title)).
 * Criterio de canonicidad: slug que empiece por "sede-sede-" primero,
 * luego mayor ID (seed más reciente).
 */

$posts = get_posts( [
    'post_type'   => 'mc_sede',
    'post_status' => 'publish',
    'numberposts' => -1,
    'orderby'     => 'ID',
    'order'       => 'ASC',
] );

$groups = [];

foreach ( $posts as $post ) {
    $terms    = wp_get_post_terms( $post->ID, 'mc_empresa', [ 'fields' => 'slugs' ] );
    $empresa  = ! empty( $terms ) ? $terms[0] : '__none__';
    $key      = $empresa . '|' . sanitize_title( $post->post_title );

    $groups[ $key ][] = [
        'ID'   => $post->ID,
        'slug' => $post->post_name,
        'title'=> $post->post_title,
        'key'  => $key,
    ];
}

$kept    = 0;
$deleted = 0;

foreach ( $groups as $key => $items ) {
    if ( count( $items ) < 2 ) {
        // No hay duplicados en este grupo — no imprimir nada.
        $kept++;
        continue;
    }

    // Ordenar: slug con prefijo "sede-sede-" primero, luego mayor ID.
    usort( $items, function( $a, $b ) {
        $a_canon = ( strpos( $a['slug'], 'sede-sede-' ) === 0 ) ? 0 : 1;
        $b_canon = ( strpos( $b['slug'], 'sede-sede-' ) === 0 ) ? 0 : 1;
        if ( $a_canon !== $b_canon ) {
            return $a_canon - $b_canon;
        }
        return $b['ID'] - $a['ID']; // mayor ID primero
    } );

    $canonical = array_shift( $items );
    WP_CLI::line( "KEEP {$canonical['ID']}\t{$canonical['slug']}\t{$canonical['title']}\tGROUP {$canonical['key']}" );
    $kept++;

    foreach ( $items as $dup ) {
        wp_delete_post( $dup['ID'], true );
        WP_CLI::line( "DEL  {$dup['ID']}\t{$dup['slug']}\t{$dup['title']}\tGROUP {$dup['key']}" );
        $deleted++;
    }
}

WP_CLI::success( "SUMMARY kept_groups={$kept} deleted_posts={$deleted}" );
