<?php
/**
 * Footer del tema MC Intranet
 * Renderiza el footer desde HFE si existe template activo.
 */
?>

<?php if ( function_exists( 'hfe_footer_enabled' ) && hfe_footer_enabled() ) : ?>
    <?php do_action( 'hfe_footer' ); ?>
<?php else : ?>
    <footer class="site-footer" role="contentinfo">
        <div class="container">
            <div class="site-footer__grid">
                <div class="site-footer__brand">
                    <p class="site-footer__brand-name">MC Intranet</p>
                    <p class="site-footer__brand-desc">
                        <?php esc_html_e( 'Portal corporativo del grupo multicompañía. Projection Anstra · Essenza Foods · Budefry.', 'mc-intranet' ); ?>
                    </p>
                </div>
                <div class="site-footer__locations-wrap">
                    <p class="site-footer__locations-title"><?php esc_html_e( 'Nuestras Sedes', 'mc-intranet' ); ?></p>
                    <div class="site-footer__locations">
                        <?php
                        // Si el plugin mc-intranet-core está activo, usar shortcode dinámico.
                        // De lo contrario renderizar sedes estáticas de fallback.
                        if ( shortcode_exists( 'mc_sedes' ) ) {
                            echo do_shortcode( '[mc_sedes]' );
                        } else {
                            get_template_part( 'template-parts/footer-locations-fallback' );
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="site-footer__bottom">
                <p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> MC Intranet &mdash; <?php bloginfo( 'name' ); ?></p>
            </div>
        </div>
    </footer>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
