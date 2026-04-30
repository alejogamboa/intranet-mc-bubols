<?php
/**
 * Alerta de contexto de empresa.
 *
 * Se muestra bajo el hero en portales de empresa para recordar al usuario
 * que los recursos mostrados son exclusivos de esa empresa.
 *
 * Uso: get_template_part( 'template-parts/context-alert' );
 * O vía shortcode del plugin: [mc_context_alert empresa="anstra"]
 *
 * @package MC_Intranet
 */

$company = mc_get_company_context();
if ( 'default' === $company ) {
    return; // No mostrar en el inicio global.
}

$config = [
    'anstra' => [
        'label'       => 'Projection Anstra',
        'alert_class' => 'alert--info',
        'title'       => 'Portal exclusivo de Projection Anstra',
        'body'        => 'Los formularios de esta sección corresponden únicamente a colaboradores de Projection Anstra. Si perteneces a otra empresa del grupo, regresa al %s y selecciona tu empresa.',
    ],
    'essenza' => [
        'label'       => 'Essenza Foods',
        'alert_class' => 'alert--info',
        'title'       => 'Portal exclusivo de Essenza Foods',
        'body'        => 'Los formularios de esta sección corresponden únicamente a colaboradores de Essenza Foods (NIT 901 971 854-7). Si perteneces a otra empresa del grupo, regresa al %s.',
    ],
    'budefry' => [
        'label'       => 'Budefry SAS',
        'alert_class' => 'alert--warning',
        'title'       => 'Portal exclusivo de Budefry SAS',
        'body'        => 'Los formularios de esta sección corresponden únicamente a colaboradores de Budefry SAS (NIT 901 565 887-9). Si operas desde planta en Guarne, usa tu dispositivo móvil. Si perteneces a otra empresa, regresa al %s.',
    ],
    'interactua' => [
        'label'       => 'Interactúa',
        'alert_class' => 'alert--info',
        'title'       => 'Portal exclusivo de Interactúa',
        'body'        => 'El contenido de esta sección corresponde a cultura corporativa y reconocimientos del grupo. Para formularios empresariales, regresa al %s.',
    ],
];

$entry = $config[ $company ] ?? null;
if ( ! $entry ) {
    return;
}

$portal_link = '<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'portal principal', 'mc-intranet' ) . '</a>';
$alert_body  = sprintf( $entry['body'], $portal_link );

$icon_markup = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>';
if ( 'budefry' === $company ) {
    $icon_markup = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m10.29 3.86-8.35 14.5A2 2 0 0 0 3.67 21h16.66a2 2 0 0 0 1.73-2.64l-8.35-14.5a2 2 0 0 0-3.42 0Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>';
}
?>
<section class="context-alert-block" aria-label="<?php echo esc_attr( $entry['label'] ); ?>">
    <div class="container">
        <div class="alert <?php echo esc_attr( $entry['alert_class'] ); ?>" role="alert">
            <?php echo $icon_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            <div>
                <p class="alert__title"><?php echo esc_html( $entry['title'] ); ?></p>
                <p class="alert__body"><?php echo wp_kses_post( $alert_body ); ?></p>
            </div>
        </div>
    </div>
</section>
