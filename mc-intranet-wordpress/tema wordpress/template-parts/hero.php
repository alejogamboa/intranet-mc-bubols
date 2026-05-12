<?php
/**
 * Hero de la página de inicio — MC Intranet.
 *
 * Los colores de empresa se aplican vía data-company en <body> (CSS custom props).
 * Para páginas de empresa el hero hereda la paleta correcta automáticamente.
 *
 * @package MC_Intranet
 */

$hero_icon_map = [
    'anstra'     => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/><path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/><path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/><path d="M10 6h4"/><path d="M10 10h4"/><path d="M10 14h4"/><path d="M10 18h4"/></svg>',
    'essenza'    => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>',
    'budefry'    => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 20a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8l-7-6H4a2 2 0 0 0-2 2Z"/><path d="M9 22V12h6v10"/><path d="M14 2v6h6"/></svg>',
    'interactua' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>',
    'default'    => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg>',
];

$company        = mc_get_company_context();
$eyebrow_icon   = $hero_icon_map[ $company ] ?? $hero_icon_map['default'];
$eyebrow_logo   = mc_get_company_hero_logo_img( $company, 'company-logo company-logo--eyebrow', '' );
$hero_logo      = mc_get_company_hero_logo_img( $company, 'company-logo company-logo--hero', '', false );
$anstra_name    = mc_get_company_display_name( 'anstra', 'Projection Anstra' );
$essenza_name   = mc_get_company_display_name( 'essenza', 'Essenza Foods' );
$budefry_name   = mc_get_company_display_name( 'budefry', 'Budefry SAS' );

$hero_company_defaults = [
    'anstra'     => [
        'hero_eyebrow'      => 'Portal Empresarial · NIT 901 967 530-0',
        'hero_title_line_1' => 'Projection',
        'hero_title_line_2' => 'Anstra',
        'hero_description'  => 'Portal de gestión administrativa, contabilidad y recursos humanos. Accede a todos los formularios y documentos internos de la empresa.',
    ],
    'essenza'    => [
        'hero_eyebrow'      => 'Portal Empresarial · NIT 901 971 854-7',
        'hero_title_line_1' => 'Essenza',
        'hero_title_line_2' => 'Foods',
        'hero_description'  => 'Portal de gestión comercial, mercadeo, marca y recursos humanos de Essenza Foods. Accede a todos tus formularios y documentos internos.',
    ],
    'budefry'    => [
        'hero_eyebrow'      => 'Portal Empresarial · NIT 901 565 887-9',
        'hero_title_line_1' => 'Budefry',
        'hero_title_line_2' => 'SAS',
        'hero_description'  => 'Portal de operación, logística y producción industrial. Accede a los formularios de recursos humanos y documentos de gestión de planta.',
    ],
    'interactua' => [
        'hero_eyebrow'      => 'Interactúa · Cultura Corporativa',
        'hero_title_line_1' => 'Interactúa',
        'hero_title_line_2' => '',
        'hero_description'  => 'Espacio de cultura corporativa, reconocimientos y eventos importantes del grupo MC.',
    ],
];

$default_eyebrow = 'Portal Transversal · Multicompañía';
$default_title   = 'Bienvenido a<br><span>MC Intranet</span>';
$default_desc    = 'Accede a los formularios, gestiones y recursos del grupo corporativo. Selecciona tu empresa o utiliza los servicios transversales.';

$eyebrow = $default_eyebrow;
$title   = $default_title;
$desc    = $default_desc;
$hero_bg = '';

if ( isset( $hero_company_defaults[ $company ] ) ) {
    $hero_settings = $hero_company_defaults[ $company ];

    if ( class_exists( 'MC_Intranet_Branding_Settings' ) ) {
        $stored_company_settings = MC_Intranet_Branding_Settings::get_company_settings( $company );
        if ( is_array( $stored_company_settings ) ) {
            $hero_settings = wp_parse_args( $stored_company_settings, $hero_settings );
        }
    }

    $eyebrow    = sanitize_text_field( (string) ( $hero_settings['hero_eyebrow'] ?? $default_eyebrow ) );
    $title_line = sanitize_text_field( (string) ( $hero_settings['hero_title_line_1'] ?? '' ) );
    $title_sub  = sanitize_text_field( (string) ( $hero_settings['hero_title_line_2'] ?? '' ) );
    $desc       = sanitize_textarea_field( (string) ( $hero_settings['hero_description'] ?? $default_desc ) );
    $hero_bg    = sanitize_hex_color( (string) ( $hero_settings['hero_bg_color'] ?? '' ) );

    if ( '' !== $title_line && '' !== $title_sub ) {
        $title = '<span>' . esc_html( $title_line ) . '</span><br>' . esc_html( $title_sub );
    } elseif ( '' !== $title_line ) {
        $title = esc_html( $title_line );
    }
}
?>

<section class="page-hero" aria-labelledby="hero-title"<?php if ( $hero_bg ) : ?> style="background:<?php echo esc_attr( $hero_bg ); ?>;"<?php endif; ?>>
    <div class="page-hero__pattern" aria-hidden="true"></div>
    <div class="container">
        <div class="page-hero__inner">
            <div class="page-hero__content">
                <span class="page-hero__eyebrow">
                    <?php if ( $eyebrow_logo ) : ?>
                        <?php echo $eyebrow_logo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <?php else : ?>
                        <?php echo $eyebrow_icon; // SVG inline, sanitized above ?>
                    <?php endif; ?>
                    <?php echo esc_html( $eyebrow ); ?>
                </span>
                <h1 class="page-hero__title" id="hero-title">
                    <?php echo wp_kses( $title, [ 'br' => [], 'span' => [] ] ); ?>
                </h1>
                <p class="page-hero__description">
                    <?php echo esc_html( $desc ); ?>
                </p>
                <?php if ( 'default' === $company ) : ?>
                <nav class="quick-access" aria-label="<?php esc_attr_e( 'Acceso rápido a empresas', 'mc-intranet' ); ?>">
                    <a href="<?php echo esc_url( home_url( '/anstra/' ) ); ?>" class="quick-access__item">
                        <?php echo mc_get_company_logo_img( 'anstra', 'company-logo company-logo--quick-access', '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        <?php echo esc_html( $anstra_name ); ?>
                    </a>
                    <a href="<?php echo esc_url( home_url( '/essenza/' ) ); ?>" class="quick-access__item">
                        <?php echo mc_get_company_logo_img( 'essenza', 'company-logo company-logo--quick-access', '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        <?php echo esc_html( $essenza_name ); ?>
                    </a>
                    <a href="<?php echo esc_url( home_url( '/budefry/' ) ); ?>" class="quick-access__item">
                        <?php echo mc_get_company_logo_img( 'budefry', 'company-logo company-logo--quick-access', '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        <?php echo esc_html( $budefry_name ); ?>
                    </a>
                    <a href="<?php echo esc_url( home_url( '/interactua/' ) ); ?>" class="quick-access__item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
                        Interactúa
                    </a>
                </nav>
                <?php endif; ?>
            </div>

            <?php if ( $hero_logo ) : ?>
            <div class="page-hero__brand" aria-hidden="true">
                <?php echo $hero_logo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>
