<?php
/**
 * Front Page — Inicio del portal MC Intranet.
 *
 * Compone la página de inicio con secciones transversales (Administración,
 * TIC, Gestiones) y grid de portales de empresa.
 * El contenido dinámico se provee via shortcodes del plugin mc-intranet-core.
 *
 * @package MC_Intranet
 */

get_header(); ?>

<main id="main" class="site-main" role="main">

    <?php
    while ( have_posts() ) :
        the_post();

        $elementor_data      = get_post_meta( get_the_ID(), '_elementor_data', true );
        $has_elementor_data  = ! empty( $elementor_data ) && '[]' !== $elementor_data;
        $is_elementor_editor = defined( 'ELEMENTOR_VERSION' ) && \Elementor\Plugin::$instance->preview->is_preview_mode();
        $is_elementor_page   = $has_elementor_data || $is_elementor_editor;

        if ( $is_elementor_page ) :
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <div class="entry-content">
                    <?php the_content(); ?>
                </div>
            </article>

            <div class="section-divider" aria-hidden="true"></div>

            <!-- Sección: Portales de empresa (siempre dinámica para respetar ajustes de branding) -->
            <section class="section-block" aria-labelledby="portals-title" style="background-color:var(--color-surface);">
                <div class="container">
                    <div class="section-header">
                        <div class="section-header__icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>
                        </div>
                        <div class="section-header__text">
                            <p class="section-header__label"><?php esc_html_e( 'Portales', 'mc-intranet' ); ?></p>
                            <h2 class="section-header__title" id="portals-title"><?php esc_html_e( 'Acceso por Empresa', 'mc-intranet' ); ?></h2>
                            <p class="section-header__desc"><?php esc_html_e( 'Selecciona tu empresa para acceder a los formularios y recursos específicos de RRHH y gestión interna.', 'mc-intranet' ); ?></p>
                        </div>
                    </div>
                    <?php echo do_shortcode( '[mc_company_portals]' ); ?>
                </div>
            </section>
            <?php
        else :
            ?>

    <?php get_template_part( 'template-parts/hero' ); ?>

    <!-- Sección: Administración -->
    <section class="section-block" aria-labelledby="admin-title">
        <div class="container">
            <div class="section-header">
                <div class="section-header__icon" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="7" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                </div>
                <div class="section-header__text">
                    <p class="section-header__label"><?php esc_html_e( 'Transversal', 'mc-intranet' ); ?></p>
                    <h2 class="section-header__title" id="admin-title"><?php esc_html_e( 'Administración', 'mc-intranet' ); ?></h2>
                    <p class="section-header__desc"><?php esc_html_e( 'Solicitudes de viajes, viáticos y hospedaje para todo el grupo corporativo.', 'mc-intranet' ); ?></p>
                </div>
            </div>
            <?php echo do_shortcode( '[mc_formularios empresa="mc" area="administracion"]' ); ?>
        </div>
    </section>

    <div class="section-divider" aria-hidden="true"></div>

    <!-- Sección: TIC -->
    <section class="section-block" aria-labelledby="tic-title">
        <div class="container">
            <div class="section-header">
                <div class="section-header__icon" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="3" rx="2"/><path d="M8 21h8"/><path d="M12 17v4"/></svg>
                </div>
                <div class="section-header__text">
                    <p class="section-header__label"><?php esc_html_e( 'Transversal', 'mc-intranet' ); ?></p>
                    <h2 class="section-header__title" id="tic-title"><?php esc_html_e( 'TIC — Tecnologías de la Información', 'mc-intranet' ); ?></h2>
                    <p class="section-header__desc"><?php esc_html_e( 'Soporte técnico, gestión de usuarios y compras tecnológicas para todo el grupo.', 'mc-intranet' ); ?></p>
                </div>
            </div>
            <?php echo do_shortcode( '[mc_formularios empresa="mc" area="tic"]' ); ?>
        </div>
    </section>

    <div class="section-divider" aria-hidden="true"></div>

    <!-- Sección: Gestiones -->
    <section class="section-block" aria-labelledby="ges-title">
        <div class="container">
            <div class="section-header">
                <div class="section-header__icon" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m16 6 4 14"/><path d="M12 6v14"/><path d="M8 8v12"/><path d="M4 4v16"/></svg>
                </div>
                <div class="section-header__text">
                    <p class="section-header__label"><?php esc_html_e( 'Transversal', 'mc-intranet' ); ?></p>
                    <h2 class="section-header__title" id="ges-title"><?php esc_html_e( 'Gestiones', 'mc-intranet' ); ?></h2>
                    <p class="section-header__desc"><?php esc_html_e( 'Servicios logísticos y trámites administrativos generales del grupo corporativo.', 'mc-intranet' ); ?></p>
                </div>
            </div>
            <?php echo do_shortcode( '[mc_formularios empresa="mc" area="gestiones"]' ); ?>
        </div>
    </section>

    <div class="section-divider" aria-hidden="true"></div>

    <!-- Sección: Portales de empresa -->
    <section class="section-block" aria-labelledby="portals-title" style="background-color:var(--color-surface);">
        <div class="container">
            <div class="section-header">
                <div class="section-header__icon" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>
                </div>
                <div class="section-header__text">
                    <p class="section-header__label"><?php esc_html_e( 'Portales', 'mc-intranet' ); ?></p>
                    <h2 class="section-header__title" id="portals-title"><?php esc_html_e( 'Acceso por Empresa', 'mc-intranet' ); ?></h2>
                    <p class="section-header__desc"><?php esc_html_e( 'Selecciona tu empresa para acceder a los formularios y recursos específicos de RRHH y gestión interna.', 'mc-intranet' ); ?></p>
                </div>
            </div>
            <?php echo do_shortcode( '[mc_company_portals]' ); ?>
        </div>
    </section>

            <?php
        endif;
    endwhile;
    ?>

</main>

<?php get_footer();
