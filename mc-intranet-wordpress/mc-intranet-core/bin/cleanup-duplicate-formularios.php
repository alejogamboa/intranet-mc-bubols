<?php
/**
 * Limpia duplicados del CPT mc_formulario.
 *
 * Regla de conservacion por grupo logico (company_context|area_context|titulo):
 * 1) Prioriza post_name que comience por "form-".
 * 2) Si hay empate, prioriza menor order_weight.
 * 3) Si hay empate, conserva el ID mas alto.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$posts = get_posts(
    [
        'post_type'   => 'mc_formulario',
        'post_status' => 'publish',
        'numberposts' => -1,
        'orderby'     => 'ID',
        'order'       => 'ASC',
    ]
);

$groups = [];
foreach ( $posts as $post ) {
    $company = (string) get_post_meta( $post->ID, 'company_context', true );
    $area    = (string) get_post_meta( $post->ID, 'area_context', true );
    $key     = $company . '|' . $area . '|' . sanitize_title( $post->post_title );

    $groups[ $key ][] = [
        'ID'           => (int) $post->ID,
        'title'        => (string) $post->post_title,
        'post_name'    => (string) $post->post_name,
        'order_weight' => (int) get_post_meta( $post->ID, 'order_weight', true ),
    ];
}

$deleted = 0;
$kept    = 0;

foreach ( $groups as $key => $items ) {
    if ( count( $items ) < 2 ) {
        continue;
    }

    usort(
        $items,
        static function ( array $a, array $b ): int {
            $a_canonical = 0 === strpos( $a['post_name'], 'form-' );
            $b_canonical = 0 === strpos( $b['post_name'], 'form-' );

            if ( $a_canonical !== $b_canonical ) {
                return $a_canonical ? -1 : 1;
            }

            if ( $a['order_weight'] !== $b['order_weight'] ) {
                return $a['order_weight'] <=> $b['order_weight'];
            }

            return $b['ID'] <=> $a['ID'];
        }
    );

    $keep = array_shift( $items );
    $kept++;

    echo 'KEEP ' . $keep['ID'] . "\t" . $keep['post_name'] . "\t" . $keep['title'] . "\tGROUP " . $key . "\n";

    foreach ( $items as $drop ) {
        wp_delete_post( $drop['ID'], true );
        $deleted++;
        echo 'DEL  ' . $drop['ID'] . "\t" . $drop['post_name'] . "\t" . $drop['title'] . "\tGROUP " . $key . "\n";
    }
}

echo "SUMMARY kept_groups={$kept} deleted_posts={$deleted}\n";
