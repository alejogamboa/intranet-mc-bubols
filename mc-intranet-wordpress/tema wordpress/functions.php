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

    // Tamaños adicionales para el hero del post individual
    add_image_size( 'sp-hero', 1600, 700, true );

    // HTML5 semántico
    add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ] );

    // Soporte explícito de Elementor (permite usar el tema con Elementor sin conflictos)
    add_theme_support( 'editor-styles' );
    add_theme_support( 'align-wide' );
    add_theme_support( 'responsive-embeds' );

    // Menús registrados
    register_nav_menus( [
        'primary' => __( 'Menú Principal', 'mc-intranet' ),
    ] );
}

// ─── Enqueue de Assets ───────────────────────────────────────────────────────
// ÚNICA fuente de carga de CSS/JS. Sin @import en style.css ni <link> en header.php.

add_action( 'wp_enqueue_scripts', 'mc_intranet_enqueue_scripts' );
function mc_intranet_enqueue_scripts() {
    $ver = '2.0.10';
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

    // 6. Oculta barra admin al hacer scroll y la muestra al volver arriba
    wp_enqueue_script( 'mc-admin-bar-scroll', $dir . '/assets/js/admin-bar-scroll.js', [], $ver, true );

    // 7. Estilos del post individual (solo en single posts)
    if ( is_single() ) {
        wp_enqueue_style( 'mc-single-post', $dir . '/assets/css/single-post.css', [ 'mc-components' ], $ver );
    }
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
 * Retorna nombre visible de empresa (editable desde admin) con fallback.
 *
 * @param string $company  Slug de empresa.
 * @param string $fallback Nombre por defecto.
 * @return string
 */
function mc_get_company_display_name( string $company, string $fallback ): string {
    $slug             = sanitize_key( $company );
    $branding_options = get_option( 'mc_intranet_company_branding', [] );

    if ( is_array( $branding_options ) && isset( $branding_options[ $slug ] ) && is_array( $branding_options[ $slug ] ) ) {
        $custom_name = sanitize_text_field( (string) ( $branding_options[ $slug ]['name'] ?? '' ) );
        if ( '' !== $custom_name ) {
            return $custom_name;
        }
    }

    return $fallback;
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

    // Prioriza logo personalizado guardado en ajustes del plugin.
    $branding_options = get_option( 'mc_intranet_company_branding', [] );
    if ( is_array( $branding_options ) && isset( $branding_options[ $slug ] ) && is_array( $branding_options[ $slug ] ) ) {
        $custom_logo_id = absint( $branding_options[ $slug ]['logo_id'] ?? 0 );
        if ( $custom_logo_id > 0 ) {
            $custom_logo_url = wp_get_attachment_image_url( $custom_logo_id, 'full' );
            if ( $custom_logo_url ) {
                return $custom_logo_url;
            }
        }
    }

    $relative = '/assets/img/logos/' . $logos[ $slug ];
    $file     = get_template_directory() . $relative;

    if ( ! file_exists( $file ) ) {
        return '';
    }

    return get_template_directory_uri() . $relative;
}

/**
 * Retorna URL del logo para Hero por slug de empresa.
 *
 * Prioridad:
 * 1) Logo Hero personalizado (hero_logo_id) desde ajustes.
 * 2) Fallback blanco del tema por compañía/contexto.
 *
 * @param string $company_slug Slug de empresa.
 * @return string
 */
function mc_get_company_hero_logo_url( string $company_slug ): string {
    $logos = [
        'anstra'     => 'anstra-blanco.png',
        'essenza'    => 'essenza-blanco.png',
        'budefry'    => 'budefry-blanco.png',
        'interactua' => 'mc-blanco.png',
        'mc'         => 'mc-blanco.png',
        'default'    => 'mc-blanco.png',
    ];

    $slug = sanitize_key( $company_slug );
    if ( ! isset( $logos[ $slug ] ) ) {
        $slug = 'default';
    }

    if ( in_array( $slug, [ 'anstra', 'essenza', 'budefry', 'interactua' ], true ) ) {
        $branding_options = get_option( 'mc_intranet_company_branding', [] );
        if ( is_array( $branding_options ) && isset( $branding_options[ $slug ] ) && is_array( $branding_options[ $slug ] ) ) {
            $custom_logo_id = absint( $branding_options[ $slug ]['hero_logo_id'] ?? 0 );
            if ( $custom_logo_id > 0 ) {
                $custom_logo_url = wp_get_attachment_image_url( $custom_logo_id, 'full' );
                if ( $custom_logo_url ) {
                    return $custom_logo_url;
                }
            }
        }
    }

    $relative = '/assets/img/logos/' . $logos[ $slug ];
    $file     = get_template_directory() . $relative;

    if ( ! file_exists( $file ) ) {
        return '';
    }

    return get_template_directory_uri() . $relative;
}

/**
 * Construye etiqueta <img> de logo para Hero.
 *
 * @param string      $company Slug o etiqueta de empresa.
 * @param string      $class   Clases CSS para la imagen.
 * @param string|null $alt     Alt del logo. Null usa etiqueta por defecto.
 * @param bool        $lazy    Si true, agrega loading="lazy".
 * @return string
 */
function mc_get_company_hero_logo_img( string $company, string $class = 'company-logo company-logo--eyebrow', ?string $alt = null, bool $lazy = true ): string {
    $slug = sanitize_key( $company );
    if ( ! in_array( $slug, [ 'anstra', 'essenza', 'budefry', 'interactua', 'mc', 'default' ], true ) ) {
        $slug = mc_get_company_slug_from_label( $company );
    }

    if ( '' === $slug ) {
        $slug = 'default';
    }

    $url = mc_get_company_hero_logo_url( $slug );
    if ( ! $url ) {
        return '';
    }

    $labels = [
        'anstra'     => 'Projection Anstra',
        'essenza'    => 'Essenza Foods',
        'budefry'    => 'Budefry SAS',
        'interactua' => 'Interactua',
        'mc'         => 'MC Intranet',
        'default'    => 'MC Intranet',
    ];

    if ( in_array( $slug, [ 'anstra', 'essenza', 'budefry', 'interactua' ], true ) ) {
        $branding_options = get_option( 'mc_intranet_company_branding', [] );
        if ( is_array( $branding_options ) && isset( $branding_options[ $slug ] ) && is_array( $branding_options[ $slug ] ) ) {
            $custom_name = sanitize_text_field( (string) ( $branding_options[ $slug ]['name'] ?? '' ) );
            if ( '' !== $custom_name ) {
                $labels[ $slug ] = $custom_name;
            }
        }
    }

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

    $branding_options = get_option( 'mc_intranet_company_branding', [] );
    if ( is_array( $branding_options ) && isset( $branding_options[ $slug ] ) && is_array( $branding_options[ $slug ] ) ) {
        $custom_name = sanitize_text_field( (string) ( $branding_options[ $slug ]['name'] ?? '' ) );
        if ( '' !== $custom_name ) {
            $labels[ $slug ] = $custom_name;
        }
    }

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

/**
 * Whitelist de etiquetas/atributos SVG permitidos para iconos de menú.
 *
 * @return array<string, array<string, bool>>
 */
function mc_intranet_allowed_menu_svg_tags(): array {
    return [
        'svg'      => [
            'xmlns'             => true,
            'viewBox'           => true,
            'width'             => true,
            'height'            => true,
            'fill'              => true,
            'stroke'            => true,
            'stroke-width'      => true,
            'stroke-linecap'    => true,
            'stroke-linejoin'   => true,
            'aria-hidden'       => true,
            'focusable'         => true,
            'class'             => true,
            'role'              => true,
        ],
        'path'     => [
            'd'                 => true,
            'fill'              => true,
            'stroke'            => true,
            'stroke-width'      => true,
            'stroke-linecap'    => true,
            'stroke-linejoin'   => true,
        ],
        'circle'   => [
            'cx'                => true,
            'cy'                => true,
            'r'                 => true,
            'fill'              => true,
            'stroke'            => true,
            'stroke-width'      => true,
        ],
        'rect'     => [
            'x'                 => true,
            'y'                 => true,
            'width'             => true,
            'height'            => true,
            'rx'                => true,
            'ry'                => true,
            'fill'              => true,
            'stroke'            => true,
            'stroke-width'      => true,
        ],
        'line'     => [
            'x1'                => true,
            'y1'                => true,
            'x2'                => true,
            'y2'                => true,
            'stroke'            => true,
            'stroke-width'      => true,
            'stroke-linecap'    => true,
        ],
        'polyline' => [
            'points'            => true,
            'fill'              => true,
            'stroke'            => true,
            'stroke-width'      => true,
            'stroke-linecap'    => true,
            'stroke-linejoin'   => true,
        ],
        'polygon'  => [
            'points'            => true,
            'fill'              => true,
            'stroke'            => true,
            'stroke-width'      => true,
            'stroke-linecap'    => true,
            'stroke-linejoin'   => true,
        ],
        'g'        => [
            'fill'              => true,
            'stroke'            => true,
            'stroke-width'      => true,
            'transform'         => true,
            'class'             => true,
        ],
        'title'    => [],
        'desc'     => [],
    ];
}

/**
 * Campo custom en Apariencia > Menús para definir icono SVG por item.
 */
add_action( 'wp_nav_menu_item_custom_fields', 'mc_intranet_menu_item_svg_field', 10, 5 );
function mc_intranet_menu_item_svg_field( $item_id, $item, $depth, $args, $current_object_id ): void {
    $svg = (string) get_post_meta( $item_id, '_menu_item_mc_svg_icon', true );
    ?>
    <p class="description description-wide">
        <label for="edit-menu-item-mc-svg-icon-<?php echo esc_attr( $item_id ); ?>">
            <?php esc_html_e( 'Icono SVG (inline)', 'mc-intranet' ); ?><br>
            <textarea
                id="edit-menu-item-mc-svg-icon-<?php echo esc_attr( $item_id ); ?>"
                class="widefat code edit-menu-item-custom"
                rows="4"
                name="menu-item-mc-svg-icon[<?php echo esc_attr( $item_id ); ?>]"><?php echo esc_textarea( $svg ); ?></textarea>
        </label>
    </p>
    <?php
}

/**
 * Guarda el SVG de cada item del menú principal.
 */
add_action( 'wp_update_nav_menu_item', 'mc_intranet_save_menu_item_svg_field', 10, 3 );
function mc_intranet_save_menu_item_svg_field( $menu_id, $menu_item_db_id, $args ): void {
    if ( ! current_user_can( 'edit_theme_options' ) ) {
        return;
    }

    if ( ! isset( $_POST['menu-item-mc-svg-icon'][ $menu_item_db_id ] ) ) {
        delete_post_meta( $menu_item_db_id, '_menu_item_mc_svg_icon' );
        return;
    }

    $raw_svg = trim( (string) wp_unslash( $_POST['menu-item-mc-svg-icon'][ $menu_item_db_id ] ) );

    if ( '' === $raw_svg ) {
        delete_post_meta( $menu_item_db_id, '_menu_item_mc_svg_icon' );
        return;
    }

    $safe_svg = wp_kses( $raw_svg, mc_intranet_allowed_menu_svg_tags() );

    if ( '' === $safe_svg ) {
        delete_post_meta( $menu_item_db_id, '_menu_item_mc_svg_icon' );
        return;
    }

    update_post_meta( $menu_item_db_id, '_menu_item_mc_svg_icon', $safe_svg );
}

/**
 * Agrega clase de estilo al <a> del menú principal.
 */
add_filter( 'nav_menu_link_attributes', 'mc_intranet_primary_nav_link_class', 10, 4 );
function mc_intranet_primary_nav_link_class( array $atts, $menu_item, $args, $depth ): array {
    if ( empty( $args->theme_location ) || 'primary' !== $args->theme_location ) {
        return $atts;
    }

    $existing      = isset( $atts['class'] ) ? (string) $atts['class'] : '';
    $atts['class'] = trim( $existing . ' global-nav__link' );

    return $atts;
}

/**
 * Renderiza el icono SVG al inicio del texto del item de menú.
 */
add_filter( 'nav_menu_item_title', 'mc_intranet_primary_nav_item_title_with_svg', 10, 4 );
function mc_intranet_primary_nav_item_title_with_svg( string $title, $menu_item, $args, $depth ): string {
    if ( empty( $args->theme_location ) || 'primary' !== $args->theme_location ) {
        return $title;
    }

    $raw_svg = (string) get_post_meta( $menu_item->ID, '_menu_item_mc_svg_icon', true );
    if ( '' === trim( $raw_svg ) ) {
        return $title;
    }

    $safe_svg = wp_kses( $raw_svg, mc_intranet_allowed_menu_svg_tags() );
    if ( '' === $safe_svg ) {
        return $title;
    }

    return sprintf(
        '<span class="global-nav__icon" aria-hidden="true">%1$s</span><span class="global-nav__text">%2$s</span>',
        $safe_svg,
        esc_html( $title )
    );
}

/**
 * Retorna true cuando el modo diagnóstico de branding está activo.
 * Solo para administradores autenticados.
 *
 * @return bool
 */
function mc_is_branding_debug_mode(): bool {
    if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
        return false;
    }

    if ( ! isset( $_GET['mc_branding_debug'] ) ) {
        return false;
    }

    $flag = sanitize_text_field( wp_unslash( (string) $_GET['mc_branding_debug'] ) );
    return '1' === $flag;
}

/**
 * Panel de diagnóstico visual para validar branding en frontend.
 * Actívalo con ?mc_branding_debug=1 (solo admins).
 */
add_action( 'wp_footer', 'mc_intranet_render_branding_debug_panel', 999 );
function mc_intranet_render_branding_debug_panel(): void {
    if ( ! mc_is_branding_debug_mode() ) {
        return;
    }

    $companies = [
        'anstra'  => 'Projection Anstra',
        'essenza' => 'Essenza Foods',
        'budefry' => 'Budefry SAS',
        'interactua' => 'Interactúa',
    ];

    $raw_options = get_option( 'mc_intranet_company_branding', [] );
    $raw_options = is_array( $raw_options ) ? $raw_options : [];

    echo '<aside style="position:fixed;right:16px;bottom:16px;z-index:99999;width:min(92vw,560px);max-height:72vh;overflow:auto;background:#0f172a;color:#e2e8f0;border:1px solid #334155;border-radius:12px;box-shadow:0 14px 30px rgba(2,6,23,.45);font:13px/1.5 -apple-system,BlinkMacSystemFont,Segoe UI,Helvetica,Arial,sans-serif;">';
    echo '<div style="padding:12px 14px;border-bottom:1px solid #334155;background:#111827;">';
    echo '<strong style="display:block;font-size:13px;">MC Branding Debug</strong>';
    echo '<span style="font-size:12px;color:#94a3b8;">Modo diagnóstico activo con ?mc_branding_debug=1</span>';
    echo '</div>';

    echo '<div style="padding:12px 14px;">';
    printf( '<p style="margin:0 0 8px;"><strong>Contexto actual:</strong> %s</p>', esc_html( mc_get_company_context() ) );
    printf( '<p style="margin:0 0 12px;"><strong>Clase de ajustes cargada:</strong> %s</p>', class_exists( 'MC_Intranet_Branding_Settings' ) ? 'SI' : 'NO' );

    foreach ( $companies as $slug => $fallback_name ) {
        $display_name = mc_get_company_display_name( $slug, $fallback_name );
        $logo_url     = mc_get_company_logo_url( $slug );

        $raw_company = isset( $raw_options[ $slug ] ) && is_array( $raw_options[ $slug ] ) ? $raw_options[ $slug ] : [];
        $raw_name    = sanitize_text_field( (string) ( $raw_company['name'] ?? '' ) );
        $raw_bg      = sanitize_hex_color( (string) ( $raw_company['header_bg_color'] ?? '' ) );
        $raw_text    = sanitize_hex_color( (string) ( $raw_company['header_text_color'] ?? '' ) );
        $raw_logo_id = absint( $raw_company['logo_id'] ?? 0 );
        $raw_hero_logo_id = absint( $raw_company['hero_logo_id'] ?? 0 );
        $raw_eyebrow = sanitize_text_field( (string) ( $raw_company['hero_eyebrow'] ?? '' ) );
        $raw_title_1 = sanitize_text_field( (string) ( $raw_company['hero_title_line_1'] ?? '' ) );
        $raw_title_2 = sanitize_text_field( (string) ( $raw_company['hero_title_line_2'] ?? '' ) );
        $raw_desc    = sanitize_textarea_field( (string) ( $raw_company['hero_description'] ?? '' ) );
        $raw_hero_bg = sanitize_hex_color( (string) ( $raw_company['hero_bg_color'] ?? '' ) );
        $hero_logo_url = mc_get_company_hero_logo_url( $slug );

        echo '<div style="padding:10px 10px 11px;border:1px solid #334155;border-radius:10px;background:#111827;margin-bottom:10px;">';
        printf( '<p style="margin:0 0 8px;"><strong>%1$s</strong> (<code style="color:#93c5fd;">%2$s</code>)</p>', esc_html( $display_name ), esc_html( $slug ) );
        printf( '<p style="margin:0 0 6px;"><strong>Nombre guardado:</strong> %s</p>', esc_html( '' !== $raw_name ? $raw_name : '(vacio)' ) );
        printf( '<p style="margin:0 0 6px;"><strong>Fondo encabezado:</strong> %s</p>', esc_html( $raw_bg ?: '(vacio)' ) );
        printf( '<p style="margin:0 0 6px;"><strong>Texto encabezado:</strong> %s</p>', esc_html( $raw_text ?: '(vacio)' ) );
        printf( '<p style="margin:0 0 6px;"><strong>Logo ID:</strong> %d</p>', $raw_logo_id );
        printf( '<p style="margin:0 0 6px;"><strong>Hero logo ID:</strong> %d</p>', $raw_hero_logo_id );
        printf( '<p style="margin:0 0 6px;"><strong>Hero fondo:</strong> %s</p>', esc_html( $raw_hero_bg ?: '(vacio)' ) );
        printf( '<p style="margin:0 0 6px;"><strong>Hero eyebrow:</strong> %s</p>', esc_html( $raw_eyebrow ?: '(vacio)' ) );
        printf( '<p style="margin:0 0 6px;"><strong>Hero título L1:</strong> %s</p>', esc_html( $raw_title_1 ?: '(vacio)' ) );
        printf( '<p style="margin:0 0 6px;"><strong>Hero título L2:</strong> %s</p>', esc_html( $raw_title_2 ?: '(vacio)' ) );
        printf( '<p style="margin:0 0 6px;"><strong>Hero descripción:</strong> %s</p>', esc_html( $raw_desc ?: '(vacio)' ) );
        printf( '<p style="margin:0;"><strong>URL logo resuelta:</strong> %s</p>', esc_html( $logo_url ?: '(sin logo)' ) );
        printf( '<p style="margin:6px 0 0;"><strong>URL logo hero:</strong> %s</p>', esc_html( $hero_logo_url ?: '(sin logo)' ) );
        echo '</div>';
    }

    echo '<p style="margin:0;color:#94a3b8;font-size:12px;">Tip: para salir del modo debug, elimina el parametro <code style="color:#93c5fd;">mc_branding_debug=1</code> de la URL.</p>';
    echo '</div>';
    echo '</aside>';
}
