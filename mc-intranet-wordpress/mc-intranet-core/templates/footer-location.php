<?php
/**
 * Template: footer-location.php
 * Variables disponibles: $sede_data (array)
 *
 * @package MC_Intranet_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$company_label   = (string) ( $sede_data['company'] ?? '' );
$company_slug    = function_exists( 'mc_get_company_slug_from_label' ) ? mc_get_company_slug_from_label( $company_label ) : '';
$sede_logo_id    = absint( (string) ( $sede_data['logo_id'] ?? 0 ) );
$sede_font_color = sanitize_hex_color( (string) ( $sede_data['font_color'] ?? '' ) );
$sede_logo       = $sede_logo_id > 0
    ? wp_get_attachment_image( $sede_logo_id, 'medium', false, [
        'class'    => 'company-logo company-logo--location',
        'loading'  => 'lazy',
        'decoding' => 'async',
    ] )
    : '';
$company_logo  = $sede_logo ? $sede_logo : ( function_exists( 'mc_get_company_logo_img' ) ? mc_get_company_logo_img( $company_slug, 'company-logo company-logo--location', '' ) : '' );
$icon_class    = $company_logo ? 'location-card__icon location-card__icon--brand' : 'location-card__icon';
?>
<div class="location-card"<?php if ( $sede_font_color ) : ?> style="--sede-font-color:<?php echo esc_attr( $sede_font_color ); ?>;"<?php endif; ?>>
    <div class="<?php echo esc_attr( $icon_class ); ?>" aria-hidden="true">
        <?php if ( $company_logo ) : ?>
            <?php echo $company_logo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <?php else : ?>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
        <?php endif; ?>
    </div>
    <?php if ( $sede_data['company'] ) : ?>
    <p class="location-card__company"><?php echo esc_html( $sede_data['company'] ); ?></p>
    <?php endif; ?>
    <p class="location-card__name"><?php echo esc_html( $sede_data['name'] ); ?></p>
    <p class="location-card__address"><?php echo nl2br( esc_html( $sede_data['address'] ) ); ?></p>
    <?php if ( $sede_data['maps_url'] ) : ?>
    <a
        href="<?php echo esc_url( $sede_data['maps_url'] ); ?>"
        target="_blank"
        rel="noopener noreferrer"
        class="location-card__link"
        aria-label="<?php echo esc_attr( sprintf( '%s en Google Maps (abre en nueva ventana)', $sede_data['name'] ) ); ?>"
    >
        <?php esc_html_e( 'Ver en Maps', 'mc-intranet-core' ); ?>
        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 3h6v6"/><path d="M10 14 21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg>
        <span class="sr-only"><?php esc_html_e( '(abre en nueva ventana)', 'mc-intranet-core' ); ?></span>
    </a>
    <?php endif; ?>
</div>
