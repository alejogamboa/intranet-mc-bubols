<?php
/**
 * Sedes del footer — fallback estático.
 *
 * Se usa solo cuando el plugin mc-intranet-core no está activo.
 * En producción los datos deben venir del shortcode [mc_sedes].
 *
 * @package MC_Intranet
 */
$sedes = [
    [
        'company' => 'Administrativa',
        'name'    => 'Sede Administrativa',
        'address' => "Calle 49 # 77A - 19\nBarrio Laureles (Estadio)\nMedellín, Antioquia",
        'maps'    => 'https://maps.google.com',
    ],
    [
        'company' => 'Essenza Foods',
        'name'    => 'Sede Comercial',
        'address' => "Carrera 74 # 48B - 59\nBarrio Laureles (Estadio)\nMedellín, Antioquia",
        'maps'    => 'https://maps.google.com',
    ],
    [
        'company' => 'Budefry SAS',
        'name'    => 'Sede Producción',
        'address' => "Vía Guarne km 2\nGuarne, Antioquia",
        'maps'    => 'https://maps.google.com',
    ],
];
foreach ( $sedes as $sede ) :
    $company_slug = function_exists( 'mc_get_company_slug_from_label' ) ? mc_get_company_slug_from_label( (string) $sede['company'] ) : '';
    $company_logo = function_exists( 'mc_get_company_logo_img' ) ? mc_get_company_logo_img( $company_slug, 'company-logo company-logo--location', '' ) : '';
?>
<div class="location-card">
    <div class="location-card__icon" aria-hidden="true">
        <?php if ( $company_logo ) : ?>
            <?php echo $company_logo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <?php else : ?>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
        <?php endif; ?>
    </div>
    <p class="location-card__company"><?php echo esc_html( $sede['company'] ); ?></p>
    <p class="location-card__name"><?php echo esc_html( $sede['name'] ); ?></p>
    <p class="location-card__address"><?php echo nl2br( esc_html( $sede['address'] ) ); ?></p>
    <a href="<?php echo esc_url( $sede['maps'] ); ?>" target="_blank" rel="noopener noreferrer" class="location-card__link"
       aria-label="<?php echo esc_attr( 'Ver ' . $sede['name'] . ' en Google Maps (abre en nueva ventana)' ); ?>">
        Ver en Maps
        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 3h6v6"/><path d="M10 14 21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg>
        <span class="sr-only">(abre en nueva ventana)</span>
    </a>
</div>
<?php endforeach; ?>
