<?php
/**
 * Template: form-card.php
 * Variables disponibles: $form_data (array)
 *
 * @package MC_Intranet_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$has_url  = ! empty( $form_data['form_url'] );
$featured = ! empty( $form_data['is_featured'] );
$new_tab  = ! empty( $form_data['open_new_tab'] );

$type_labels = [
    'form'       => 'Google Forms',
    'doc'        => 'Documento',
    'integrated' => 'Integrado',
];
$type_label = $type_labels[ $form_data['form_type'] ?? 'form' ] ?? 'Formulario';

$title_key = sanitize_title( (string) ( $form_data['title'] ?? '' ) );
$icon_svg  = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>';

if ( false !== strpos( $title_key, 'tiquetes' ) ) {
    $icon_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 16l20-8-8 20-3-9-9-3z"/></svg>';
} elseif ( false !== strpos( $title_key, 'viaticos' ) ) {
    $icon_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="6" width="20" height="12" rx="2"/><path d="M2 10h20"/><path d="M7 14h4"/></svg>';
} elseif ( false !== strpos( $title_key, 'hospedaje' ) ) {
    $icon_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 20V8a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v12"/><path d="M3 12h18"/><path d="M7 10v2"/><path d="M17 10v2"/></svg>';
} elseif ( false !== strpos( $title_key, 'soporte-tic' ) || false !== strpos( $title_key, 'soporte' ) ) {
    $icon_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 8v4"/><path d="M9 11h6"/><path d="M12 16h.01"/></svg>';
} elseif ( false !== strpos( $title_key, 'usuarios' ) ) {
    $icon_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3"/><path d="M4 19a5 5 0 0 1 10 0"/><path d="M19 8v6"/><path d="M16 11h6"/></svg>';
} elseif ( false !== strpos( $title_key, 'compras' ) ) {
    $icon_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="20" r="1"/><circle cx="18" cy="20" r="1"/><path d="M3 4h2l2.4 10.2a2 2 0 0 0 2 1.6h8.7a2 2 0 0 0 2-1.5L22 7H7"/></svg>';
} elseif ( false !== strpos( $title_key, 'logistico' ) ) {
    $icon_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 17h4"/><path d="M2 7h15v10H2z"/><path d="M17 10h3l2 3v4h-5z"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/></svg>';
} elseif ( false !== strpos( $title_key, 'votacion' ) ) {
    $icon_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="17" rx="2"/><path d="M8 9h8"/><path d="m9 14 2 2 4-4"/></svg>';
}

$card_class = 'form-card';
if ( $featured ) {
    $card_class .= ' form-card--featured';
}
if ( ! $has_url ) {
    $card_class .= ' form-card--pending';
}
?>
<article class="<?php echo esc_attr( $card_class ); ?>">
    <div class="form-card__icon" aria-hidden="true">
        <?php echo $icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
    </div>
    <div class="form-card__body">
        <h3 class="form-card__title"><?php echo esc_html( $form_data['title'] ); ?></h3>
        <?php if ( $form_data['description'] ) : ?>
        <p class="form-card__desc"><?php echo esc_html( $form_data['description'] ); ?></p>
        <?php endif; ?>
        <span class="form-card__badge <?php echo $has_url ? 'form-card__badge--form' : 'form-card__badge--pending'; ?>">
            <?php echo $has_url ? esc_html( $type_label ) : esc_html__( 'Próximamente', 'mc-intranet-core' ); ?>
        </span>
    </div>
    <div class="form-card__footer">
        <?php if ( $has_url ) : ?>
        <a
            href="<?php echo esc_url( $form_data['form_url'] ); ?>"
            <?php if ( $new_tab ) : ?>target="_blank" rel="noopener noreferrer"<?php endif; ?>
            class="btn btn-outline btn-full"
            aria-label="<?php echo esc_attr( sprintf( '%s — %s', $form_data['cta_label'], $form_data['title'] ) ); ?>"
        >
            <?php echo esc_html( $form_data['cta_label'] ); ?>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            <?php if ( $new_tab ) : ?>
            <span class="sr-only"><?php esc_html_e( '(abre en nueva ventana)', 'mc-intranet-core' ); ?></span>
            <?php endif; ?>
        </a>
        <?php else : ?>
        <button class="btn btn-outline btn-full" disabled aria-disabled="true">
            <?php esc_html_e( 'Próximamente disponible', 'mc-intranet-core' ); ?>
        </button>
        <?php endif; ?>
    </div>
</article>
