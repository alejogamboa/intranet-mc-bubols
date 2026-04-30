<?php
/**
 * Template para páginas estándar — portales por empresa y Interactúa.
 *
 * El layout visual se compone en Elementor usando shortcodes del plugin
 * mc-intranet-core. Este template solo provee el wrapper mínimo.
 *
 * @package MC_Intranet
 */

get_header(); ?>

<main id="main" class="site-main" role="main">
    <?php
    while ( have_posts() ) :
        the_post();

        $company_context   = mc_get_company_context();
        $content           = (string) get_the_content();
        $has_inline_hero   = false !== strpos( $content, 'page-hero' );
        $should_render_hero = 'default' !== $company_context && ! $has_inline_hero;

        if ( $should_render_hero ) {
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
