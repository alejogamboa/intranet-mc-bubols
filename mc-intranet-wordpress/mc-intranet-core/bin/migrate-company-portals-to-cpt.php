<?php
/**
 * Migra portales hardcodeados del shortcode [mc_company_portals] al CPT mc_company_portal.
 *
 * Uso (dry-run por defecto):
 *   wp eval-file wp-content/plugins/mc-intranet-core/bin/migrate-company-portals-to-cpt.php
 *
 * Aplicar cambios reales:
 *   wp eval-file wp-content/plugins/mc-intranet-core/bin/migrate-company-portals-to-cpt.php -- --apply
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$argv = isset( $_SERVER['argv'] ) && is_array( $_SERVER['argv'] ) ? $_SERVER['argv'] : [];
$apply_changes = in_array( '--apply', $argv, true );

$log = static function ( string $message ): void {
    if ( class_exists( 'WP_CLI' ) ) {
        WP_CLI::line( $message );
        return;
    }

    echo $message . PHP_EOL;
};

$warn = static function ( string $message ): void {
    if ( class_exists( 'WP_CLI' ) ) {
        WP_CLI::warning( $message );
        return;
    }

    echo 'WARNING: ' . $message . PHP_EOL;
};

$success = static function ( string $message ): void {
    if ( class_exists( 'WP_CLI' ) ) {
        WP_CLI::success( $message );
        return;
    }

    echo 'SUCCESS: ' . $message . PHP_EOL;
};

if ( ! post_type_exists( 'mc_company_portal' ) ) {
    $warn( 'El CPT mc_company_portal no existe. Activa/actualiza el plugin mc-intranet-core antes de ejecutar la migracion.' );
    return;
}

$portals = [
    [
        'slug'              => 'anstra',
        'name'              => 'Projection Anstra',
        'desc'              => 'Gestión administrativa, contabilidad y recursos humanos del grupo corporativo.',
        'color_start'       => '#1A2E52',
        'color_end'         => '#253E6E',
        'link_color'        => '#1A2E52',
        'header_bg_color'   => '#FFFFFF',
        'header_text_color' => '#0F172A',
        'url'               => home_url( '/anstra/' ),
        'tags'              => [ 'RRHH', 'Contabilidad', 'Administración' ],
        'count_label'       => '4 formularios',
    ],
    [
        'slug'              => 'essenza',
        'name'              => 'Essenza Foods',
        'desc'              => 'Gestión de marca, comercial, mercadeo y ventas de la línea de alimentos.',
        'color_start'       => '#1B6B45',
        'color_end'         => '#237D53',
        'link_color'        => '#1B6B45',
        'header_bg_color'   => '#FFFFFF',
        'header_text_color' => '#0F172A',
        'url'               => home_url( '/essenza/' ),
        'tags'              => [ 'RRHH', 'Comercial', 'Mercadeo' ],
        'count_label'       => '4 formularios',
    ],
    [
        'slug'              => 'budefry',
        'name'              => 'Budefry SAS',
        'desc'              => 'Operación, logística y procesos de producción industrial.',
        'color_start'       => '#2D3748',
        'color_end'         => '#3D4F66',
        'link_color'        => '#2D3748',
        'header_bg_color'   => '#FFFFFF',
        'header_text_color' => '#0F172A',
        'url'               => home_url( '/budefry/' ),
        'tags'              => [ 'RRHH', 'Producción', 'Operaciones' ],
        'count_label'       => '4 formularios',
    ],
    [
        'slug'              => 'interactua',
        'name'              => 'Interactúa',
        'desc'              => 'Cultura corporativa, employer branding, reconocimientos y eventos del grupo.',
        'color_start'       => '#4338CA',
        'color_end'         => '#5048D6',
        'link_color'        => '#4338CA',
        'header_bg_color'   => '#FFFFFF',
        'header_text_color' => '#0F172A',
        'url'               => home_url( '/interactua/' ),
        'tags'              => [ 'Reconocimientos', 'Eventos', 'Cultura' ],
        'count_label'       => 'Novedades',
    ],
];

$created = 0;
$updated = 0;
$errors  = 0;

$log( $apply_changes ? 'Modo APPLY: se guardaran cambios.' : 'Modo DRY-RUN: no se guardaran cambios (usa -- --apply para aplicar).' );

foreach ( $portals as $index => $portal ) {
    $slug = sanitize_key( (string) $portal['slug'] );

    if ( '' === $slug ) {
        $warn( 'Se omite portal sin slug valido.' );
        $errors++;
        continue;
    }

    $matches = get_posts( [
        'post_type'      => 'mc_company_portal',
        'post_status'    => [ 'publish', 'draft', 'pending', 'private', 'future' ],
        'posts_per_page' => -1,
        'orderby'        => 'ID',
        'order'          => 'DESC',
        'meta_query'     => [
            [
                'key'     => 'portal_slug',
                'value'   => $slug,
                'compare' => '=',
            ],
        ],
        'fields'         => 'ids',
    ] );

    $post_id = 0;
    $action  = 'create';

    if ( ! empty( $matches ) ) {
        $post_id = (int) $matches[0];
        $action  = 'update';

        if ( count( $matches ) > 1 ) {
            $warn( sprintf( 'Slug "%s" tiene %d posts. Se actualiza el mas reciente (ID %d).', $slug, count( $matches ), $post_id ) );
        }
    }

    $postarr = [
        'post_type'   => 'mc_company_portal',
        'post_title'  => sanitize_text_field( (string) $portal['name'] ),
        'post_name'   => $slug,
        'menu_order'  => $index,
        'post_status' => 'publish',
    ];

    if ( 'update' === $action ) {
        $postarr['ID'] = $post_id;
    }

    if ( $apply_changes ) {
        $result = 'update' === $action ? wp_update_post( $postarr, true ) : wp_insert_post( $postarr, true );

        if ( is_wp_error( $result ) ) {
            $warn( sprintf( '%s %s fallo: %s', strtoupper( $action ), $slug, $result->get_error_message() ) );
            $errors++;
            continue;
        }

        $post_id = (int) $result;

        update_post_meta( $post_id, 'portal_slug', $slug );
        update_post_meta( $post_id, 'portal_name', sanitize_text_field( (string) $portal['name'] ) );
        update_post_meta( $post_id, 'portal_desc', sanitize_textarea_field( (string) $portal['desc'] ) );
        update_post_meta( $post_id, 'portal_color_start', sanitize_hex_color( (string) $portal['color_start'] ) ?: '#1A2E52' );
        update_post_meta( $post_id, 'portal_color_end', sanitize_hex_color( (string) $portal['color_end'] ) ?: '#253E6E' );
        update_post_meta( $post_id, 'portal_link_color', sanitize_hex_color( (string) $portal['link_color'] ) ?: '#1A2E52' );
        update_post_meta( $post_id, 'portal_header_bg_color', sanitize_hex_color( (string) $portal['header_bg_color'] ) ?: '#FFFFFF' );
        update_post_meta( $post_id, 'portal_header_text_color', sanitize_hex_color( (string) $portal['header_text_color'] ) ?: '#0F172A' );
        update_post_meta( $post_id, 'portal_url', esc_url_raw( (string) $portal['url'] ) );
        update_post_meta( $post_id, 'portal_tags', implode( ', ', array_map( 'sanitize_text_field', (array) $portal['tags'] ) ) );
        update_post_meta( $post_id, 'portal_count_label', sanitize_text_field( (string) $portal['count_label'] ) );
    }

    if ( 'update' === $action ) {
        $updated++;
    } else {
        $created++;
    }

    $log( sprintf( '%s slug=%s id=%d title="%s"', strtoupper( $action ), $slug, $post_id, $postarr['post_title'] ) );
}

$summary = sprintf( 'SUMMARY created=%d updated=%d errors=%d mode=%s', $created, $updated, $errors, $apply_changes ? 'apply' : 'dry-run' );

if ( 0 === $errors ) {
    $success( $summary );
} else {
    $warn( $summary );
}
