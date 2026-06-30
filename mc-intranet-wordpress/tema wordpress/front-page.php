<?php
/**
 * Front Page template.
 *
 * La portada se administra 100% desde Elementor (contenido de la página).
 *
 * @package MC_Intranet
 */

get_header();
?>

<main id="main" class="site-main" role="main">
    <?php
    while ( have_posts() ) :
        the_post();

        $raw_content    = (string) get_the_content();
        $elementor_data = (string) get_post_meta( get_the_ID(), '_elementor_data', true );
        $has_inline_hero = false !== strpos( $raw_content, 'page-hero' ) || false !== strpos( $elementor_data, 'page-hero' );

        if ( ! $has_inline_hero ) {
            get_template_part( 'template-parts/hero' );
        }
        ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <div class="entry-content">
                <?php the_content(); ?>
            </div>
        </article>
        <?php
    endwhile;
    ?>
</main>

<?php get_footer();
