<?php
$posts = get_posts([
    'post_type' => 'mc_formulario',
    'post_status' => 'publish',
    'numberposts' => -1,
    'orderby' => 'ID',
    'order' => 'ASC',
]);

$groups = [];
foreach ( $posts as $post ) {
    $company = (string) get_post_meta( $post->ID, 'company_context', true );
    $area    = (string) get_post_meta( $post->ID, 'area_context', true );
    $key     = $company . '|' . $area . '|' . sanitize_title( $post->post_title );

    $groups[ $key ][] = [
        'ID'    => $post->ID,
        'title' => $post->post_title,
        'slug'  => $post->post_name,
    ];
}

foreach ( $groups as $key => $items ) {
    if ( count( $items ) < 2 ) {
        continue;
    }

    echo "GROUP: {$key}\n";
    foreach ( $items as $item ) {
        echo $item['ID'] . "\t" . $item['slug'] . "\t" . $item['title'] . "\n";
    }
    echo "---\n";
}
