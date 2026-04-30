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
$eyebrow_logo   = mc_get_company_logo_img( $company, 'company-logo company-logo--eyebrow', '' );

$eyebrow_map    = [
    'anstra'     => 'Portal Empresarial · NIT 901 967 530-0',
    'essenza'    => 'Portal Empresarial · NIT 901 971 854-7',
    'budefry'    => 'Portal Empresarial · NIT 901 565 887-9',
    'interactua' => 'Interactúa · Cultura Corporativa',
    'default'    => 'Portal Transversal · Multicompañía',
];

$title_map = [
    'anstra'     => '<span>Projection</span><br>Anstra',
    'essenza'    => '<span>Essenza</span><br>Foods',
    'budefry'    => '<span>Budefry</span><br>SAS',
    'interactua' => 'Interactúa',
    'default'    => 'Bienvenido a<br><span>MC Intranet</span>',
];

$desc_map = [
    'anstra'     => 'Portal de gestión administrativa, contabilidad y recursos humanos. Accede a todos los formularios y documentos internos de la empresa.',
    'essenza'    => 'Portal de gestión comercial, mercadeo, marca y recursos humanos de Essenza Foods. Accede a todos tus formularios y documentos internos.',
    'budefry'    => 'Portal de operación, logística y producción industrial. Accede a los formularios de recursos humanos y documentos de gestión de planta.',
    'interactua' => 'Espacio de cultura corporativa, reconocimientos y eventos importantes del grupo MC.',
    'default'    => 'Accede a los formularios, gestiones y recursos del grupo corporativo. Selecciona tu empresa o utiliza los servicios transversales.',
];

$eyebrow = $eyebrow_map[ $company ] ?? $eyebrow_map['default'];
$title   = $title_map[ $company ]   ?? $title_map['default'];
$desc    = $desc_map[ $company ]    ?? $desc_map['default'];
?>

<section class="page-hero" aria-labelledby="hero-title">
    <div class="page-hero__pattern" aria-hidden="true"></div>
    <div class="container">
        <div class="page-hero__inner">
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
                    Projection Anstra
                </a>
                <a href="<?php echo esc_url( home_url( '/essenza/' ) ); ?>" class="quick-access__item">
                    <?php echo mc_get_company_logo_img( 'essenza', 'company-logo company-logo--quick-access', '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    Essenza Foods
                </a>
                <a href="<?php echo esc_url( home_url( '/budefry/' ) ); ?>" class="quick-access__item">
                    <?php echo mc_get_company_logo_img( 'budefry', 'company-logo company-logo--quick-access', '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    Budefry
                </a>
                <a href="<?php echo esc_url( home_url( '/interactua/' ) ); ?>" class="quick-access__item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
                    Interactúa
                </a>
            </nav>
            <?php endif; ?>
        </div>
    </div>
</section>
