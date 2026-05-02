<?php
/**
 * MC Intranet Theme — functions.php
 *
 * Registro de soporte del tema, menús, enqueues y helpers de contexto.
 * REGLA: La lógica de negocio va en el plugin mc-intranet-core, NO aquí.
 */

// ─── Theme Setup ─────────────────────────────────────────────────────────────

add_action( 'after_setup_theme', 'mc_intranet_theme_setup' );
function mc_intranet_theme_setup() {
    // Soporte de título administrado por WordPress (evita hardcoded <title>)
    add_theme_support( 'title-tag' );

    // Imágenes destacadas
    add_theme_support( 'post-thumbnails' );

    // HTML5 semántico
    add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ] );

    // Menús registrados
    register_nav_menus( [
        'primary' => __( 'Menú Principal', 'mc-intranet' ),
    ] );
}

// ─── Enqueue de Assets ───────────────────────────────────────────────────────
// ÚNICA fuente de carga de CSS/JS. Sin @import en style.css ni <link> en header.php.

add_action( 'wp_enqueue_scripts', 'mc_intranet_enqueue_scripts' );
function mc_intranet_enqueue_scripts() {
    $ver = '2.0.6';
    $dir = get_template_directory_uri();

    // 1. Design tokens (base de variables CSS — siempre primero)
    wp_enqueue_style( 'mc-design-tokens', $dir . '/assets/css/design-tokens.css', [], $ver );

    // 2. Componentes (depende de tokens)
    wp_enqueue_style( 'mc-components', $dir . '/assets/css/components.css', [ 'mc-design-tokens' ], $ver );

    // 3. Tema (style.css — solo metadatos, sin @import)
    wp_enqueue_style( 'mc-theme', get_stylesheet_uri(), [ 'mc-components' ], $ver );

    // 4. Hamburguesa + aria-expanded (footer, sin jQuery como dep innecesaria)
    wp_enqueue_script( 'mc-nav-toggle', $dir . '/assets/js/nav-toggle.js', [], $ver, true );

    // 5. Lightbox para galeria del shortcode [mc_eventos]
    wp_enqueue_script( 'mc-event-gallery-lightbox', $dir . '/assets/js/event-gallery-lightbox.js', [], $ver, true );
}

// ─── Contexto de empresa ─────────────────────────────────────────────────────

/**
 * Agrega la clase has-company-context al body cuando la página tiene contexto.
 */
add_filter( 'body_class', 'mc_intranet_body_class' );
function mc_intranet_body_class( $classes ) {
    $company = mc_get_company_context();
    if ( $company ) {
        $classes[] = 'has-company-context';
        $classes[] = 'company--' . sanitize_html_class( $company );
    }
    return $classes;
}

/**
 * Retorna el company_context del post actual (o 'default').
 *
 * @return string
 */
function mc_get_company_context() {
    $context = get_post_meta( get_the_ID(), 'company_context', true );
    return $context ?: 'default';
}

/**
 * Retorna el atributo data-company ya escapado para usar en <body>.
 *
 * @return string
 */
function mc_get_data_company_attr() {
    return esc_attr( mc_get_company_context() );
}

/**
 * Retorna slug de empresa inferido desde un texto de etiqueta.
 *
 * @param string $label Nombre/etiqueta de empresa.
 * @return string
 */
function mc_get_company_slug_from_label( string $label ): string {
    $normalized = sanitize_title( remove_accents( $label ) );

    if ( false !== strpos( $normalized, 'essenza' ) ) {
        return 'essenza';
    }

    if ( false !== strpos( $normalized, 'budefry' ) ) {
        return 'budefry';
    }

    if ( false !== strpos( $normalized, 'anstra' ) || false !== strpos( $normalized, 'projection' ) || false !== strpos( $normalized, 'administrativa' ) ) {
        return 'anstra';
    }

    if ( false !== strpos( $normalized, 'interactua' ) ) {
        return 'interactua';
    }

    return '';
}

/**
 * Retorna URL del logo WEBP por slug de empresa.
 *
 * @param string $company_slug Slug de empresa.
 * @return string
 */
function mc_get_company_logo_url( string $company_slug ): string {
    $logos = [
        'anstra'  => 'anstra.webp',
        'essenza' => 'essenza.webp',
        'budefry' => 'budefry.webp',
    ];

    $slug = sanitize_key( $company_slug );
    if ( ! isset( $logos[ $slug ] ) ) {
        return '';
    }

    $relative = '/assets/img/logos/' . $logos[ $slug ];
    $file     = get_template_directory() . $relative;

    if ( ! file_exists( $file ) ) {
        return '';
    }

    return get_template_directory_uri() . $relative;
}

/**
 * Construye etiqueta <img> de logo de empresa.
 *
 * @param string      $company Slug o etiqueta de empresa.
 * @param string      $class   Clases CSS para la imagen.
 * @param string|null $alt     Alt del logo. Null usa etiqueta por defecto.
 * @param bool        $lazy    Si true, agrega loading="lazy".
 * @return string
 */
function mc_get_company_logo_img( string $company, string $class = 'company-logo', ?string $alt = null, bool $lazy = true ): string {
    $slug = sanitize_key( $company );
    if ( ! in_array( $slug, [ 'anstra', 'essenza', 'budefry', 'interactua' ], true ) ) {
        $slug = mc_get_company_slug_from_label( $company );
    }

    $url = mc_get_company_logo_url( $slug );
    if ( ! $url ) {
        return '';
    }

    $labels = [
        'anstra'  => 'Projection Anstra',
        'essenza' => 'Essenza Foods',
        'budefry' => 'Budefry SAS',
    ];

    if ( null === $alt ) {
        $label = $labels[ $slug ] ?? $slug;
        $alt   = sprintf( __( 'Logo de %s', 'mc-intranet' ), $label );
    }

    $attrs = $lazy ? ' loading="lazy" decoding="async"' : ' decoding="async"';

    return sprintf(
        '<img src="%1$s" alt="%2$s" class="%3$s"%4$s>',
        esc_url( $url ),
        esc_attr( $alt ),
        esc_attr( $class ),
        $attrs
    );
}

/**
 * Fallback del menú principal cuando no hay menú asignado.
 */
function mc_intranet_nav_fallback() {
    $pages = [
        home_url( '/' )            => __( 'Inicio', 'mc-intranet' ),
        home_url( '/anstra/' )     => 'Projection Anstra',
        home_url( '/essenza/' )    => 'Essenza Foods',
        home_url( '/budefry/' )    => 'Budefry',
        home_url( '/interactua/' ) => 'Interactúa',
    ];

    echo '<ul class="global-nav__links" role="list" id="nav-links">';
    foreach ( $pages as $url => $label ) {
        printf(
            '<li><a href="%s" class="global-nav__link">%s</a></li>',
            esc_url( $url ),
            esc_html( $label )
        );
    }
    echo '</ul>';
}
