<?php
/**
 * Template: company-portal-card.php
 * Variables disponibles: $portal (array)
 *
 * @package MC_Intranet_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$portal_slug = (string) ( $portal['slug'] ?? '' );
$portal_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/></svg>';

if ( 'anstra' === $portal_slug ) {
    $portal_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="18" rx="1"/><rect x="14" y="8" width="7" height="13" rx="1"/></svg>';
} elseif ( 'essenza' === $portal_slug ) {
    $portal_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 2v4"/><path d="M18 2v4"/><rect x="3" y="6" width="18" height="15" rx="2"/><path d="M3 10h18"/></svg>';
} elseif ( 'budefry' === $portal_slug ) {
    $portal_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 21h18"/><path d="M5 21V8l7-4v17"/><path d="M12 9h7v12"/></svg>';
} elseif ( 'interactua' === $portal_slug ) {
    $portal_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v4"/><path d="M12 17v4"/><path d="M3 12h4"/><path d="M17 12h4"/><path d="m5.6 5.6 2.8 2.8"/><path d="m15.6 15.6 2.8 2.8"/><path d="m18.4 5.6-2.8 2.8"/><path d="m8.4 15.6-2.8 2.8"/></svg>';
}

$portal_logo = function_exists( 'mc_get_company_logo_img' ) ? mc_get_company_logo_img( $portal_slug, 'company-logo company-logo--portal', '' ) : '';
$logo_container_class = $portal_logo ? 'company-card__logo company-card__logo--brand' : 'company-card__logo';
$logo_container_style = $portal_logo ? '' : 'background:linear-gradient(135deg,' . esc_attr( $portal['color_start'] ) . ',' . esc_attr( $portal['color_end'] ) . ');';

$header_bg_color   = sanitize_hex_color( (string) ( $portal['header_bg_color'] ?? '' ) );
$header_text_color = sanitize_hex_color( (string) ( $portal['header_text_color'] ?? '' ) );

$header_styles = [];
if ( $header_bg_color ) {
    $header_styles[] = 'background:' . $header_bg_color;
}
if ( $header_text_color ) {
    $header_styles[] = 'color:' . $header_text_color;
}
$header_style_attr = implode( ';', $header_styles );

$text_style_attr = $header_text_color ? 'color:' . $header_text_color : '';
?>
<a href="<?php echo esc_url( $portal['url'] ); ?>" class="company-card">
    <div class="company-card__header"<?php if ( $header_style_attr ) : ?> style="<?php echo esc_attr( $header_style_attr ); ?>"<?php endif; ?>>
        <div class="<?php echo esc_attr( $logo_container_class ); ?>"<?php if ( $logo_container_style ) : ?> style="<?php echo esc_attr( $logo_container_style ); ?>"<?php endif; ?> aria-hidden="true">
            <?php if ( $portal_logo ) : ?>
                <?php echo $portal_logo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            <?php else : ?>
                <?php echo $portal_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            <?php endif; ?>
        </div>
        <div>
            <h3 class="company-card__name"<?php if ( $text_style_attr ) : ?> style="<?php echo esc_attr( $text_style_attr ); ?>"<?php endif; ?>><?php echo esc_html( $portal['name'] ); ?></h3>
            <p class="company-card__desc"<?php if ( $text_style_attr ) : ?> style="<?php echo esc_attr( $text_style_attr ); ?>"<?php endif; ?>><?php echo esc_html( $portal['desc'] ); ?></p>
        </div>
    </div>
    <div class="company-card__body">
        <div class="company-card__tags">
            <?php foreach ( $portal['tags'] as $tag ) : ?>
            <span class="tag"><?php echo esc_html( $tag ); ?></span>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="company-card__footer">
        <span class="company-card__link" style="color:<?php echo esc_attr( $portal['link_color'] ); ?>;">
            <?php esc_html_e( 'Ver portal', 'mc-intranet-core' ); ?>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
        </span>
        <span style="font-size:var(--text-xs,0.75rem);color:var(--color-text-muted,#94A3B8);"><?php echo esc_html( $portal['count_label'] ); ?></span>
    </div>
</a>
