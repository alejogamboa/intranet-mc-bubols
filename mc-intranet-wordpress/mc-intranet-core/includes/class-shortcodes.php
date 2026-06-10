<?php
/**
 * Shortcodes — MC Intranet Core.
 *
 * Shortcodes registrados:
 *  [mc_formularios]       → Grid de form-cards filtrado por empresa y área
 *  [mc_company_portals]   → Grid de portales de empresa
 *  [mc_sedes]             → Footer locations
 *  [mc_reconocimientos]   → Grid de recognition-cards
 *  [mc_eventos]           → Timeline de eventos
 *  [mc_context_alert]     → Alerta de contexto de empresa
 *
 * @package MC_Intranet_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MC_Intranet_Shortcodes {

    public function __construct() {
        add_shortcode( 'mc_formularios',              [ $this, 'shortcode_formularios' ] );
        add_shortcode( 'mc_company_portals',          [ $this, 'shortcode_company_portals' ] );
        add_shortcode( 'mc_sedes',                    [ $this, 'shortcode_sedes' ] );
        add_shortcode( 'mc_sede',                     [ $this, 'shortcode_sedes' ] );
        add_shortcode( 'mc_reconocimientos',          [ $this, 'shortcode_reconocimientos' ] );
        add_shortcode( 'mc_eventos',                  [ $this, 'shortcode_eventos' ] );
        add_shortcode( 'mc_context_alert',            [ $this, 'shortcode_context_alert' ] );
        add_shortcode( 'mc_directorio_contactos',     [ $this, 'shortcode_directorio_contactos' ] );
        add_shortcode( 'mc_login_screen',             [ $this, 'shortcode_login_screen' ] );
        add_shortcode( 'mc_access_denied',            [ $this, 'shortcode_access_denied' ] );
        add_shortcode( 'display-posts',               [ $this, 'shortcode_display_posts' ] );
    }

    // ─── [mc_formularios] ────────────────────────────────────────────────────

    /**
     * @param array $atts {
     *   @type string $empresa  Slug de empresa: mc|anstra|essenza|budefry. Default: mc.
     *   @type string $area     Slug de área:    administracion|tic|gestiones|rrhh. Default: ''.
     *   @type string $featured 'yes' para solo destacados. Default: ''.
     * }
     */
    public function shortcode_formularios( $atts ): string {
        $atts = shortcode_atts( [
            'empresa'  => 'mc',
            'area'     => '',
            'featured' => '',
        ], $atts, 'mc_formularios' );

        // Sanitizar
        $empresa  = sanitize_key( $atts['empresa'] );
        $area     = sanitize_key( $atts['area'] );
        $featured = 'yes' === sanitize_key( $atts['featured'] );

        $tax_query = [
            'relation' => 'AND',
            [
                'taxonomy' => 'mc_empresa',
                'field'    => 'slug',
                'terms'    => $empresa,
            ],
        ];

        if ( $area ) {
            $tax_query[] = [
                'taxonomy' => 'mc_area',
                'field'    => 'slug',
                'terms'    => $area,
            ];
        }

        $meta_query = [ 'relation' => 'AND' ];
        if ( $featured ) {
            $meta_query[] = [
                'key'     => 'is_featured',
                'value'   => '1',
                'compare' => '=',
            ];
        }

        $query = new WP_Query( [
            'post_type'      => 'mc_formulario',
            'posts_per_page' => 50,
            'post_status'    => 'publish',
            'tax_query'      => $tax_query,
            'meta_query'     => $meta_query,
            'meta_key'       => 'order_weight',
            'orderby'        => 'meta_value_num',
            'order'          => 'ASC',
        ] );

        if ( ! $query->have_posts() ) {
            return '';
        }

        $forms = [];

        while ( $query->have_posts() ) {
            $query->the_post();

            $form_id      = get_the_ID();
            $company_slug = (string) get_post_meta( $form_id, 'company_context', true );
            $area_slug    = (string) get_post_meta( $form_id, 'area_context', true );

            $form_data = [
                'id'           => $form_id,
                'title'        => get_the_title(),
                'description'  => get_the_excerpt(),
                'form_url'     => esc_url_raw( (string) get_post_meta( $form_id, 'form_url', true ) ),
                'cta_label'    => (string) get_post_meta( $form_id, 'cta_label', true ) ?: __( 'Abrir formulario', 'mc-intranet-core' ),
                'open_new_tab' => (bool) get_post_meta( $form_id, 'open_new_tab', true ),
                'is_featured'  => (bool) get_post_meta( $form_id, 'is_featured', true ),
                'form_type'    => (string) get_post_meta( $form_id, 'form_type', true ) ?: 'form',
                'post_name'    => (string) get_post_field( 'post_name', $form_id ),
                'company'      => $company_slug,
                'area'         => $area_slug,
                'order_weight' => (int) get_post_meta( $form_id, 'order_weight', true ),
            ];

            $logical_key = $this->get_form_logical_key( $form_data, $empresa, $area );

            if ( ! isset( $forms[ $logical_key ] ) || $this->is_better_form_candidate( $form_data, $forms[ $logical_key ] ) ) {
                $forms[ $logical_key ] = $form_data;
            }
        }
        wp_reset_postdata();

        if ( empty( $forms ) ) {
            return '';
        }

        uasort(
            $forms,
            static function ( array $left, array $right ): int {
                $weight_compare = $left['order_weight'] <=> $right['order_weight'];

                if ( 0 !== $weight_compare ) {
                    return $weight_compare;
                }

                return strcasecmp( $left['title'], $right['title'] );
            }
        );

        ob_start();
        echo '<div class="form-cards-grid">';
        foreach ( $forms as $form_data ) {
            include MC_CORE_TEMPLATES . 'form-card.php';
        }
        echo '</div>';

        return ob_get_clean();
    }

    private function get_form_logical_key( array $form_data, string $fallback_company, string $fallback_area ): string {
        $title   = sanitize_title( $form_data['title'] ?? '' );
        $company = sanitize_key( $form_data['company'] ?: $fallback_company );
        $area    = sanitize_key( $form_data['area'] ?: $fallback_area );

        return implode( '|', [ $company, $area, $title ] );
    }

    private function is_better_form_candidate( array $candidate, array $current ): bool {
        $candidate_is_canonical = 0 === strpos( $candidate['post_name'], 'form-' );
        $current_is_canonical   = 0 === strpos( $current['post_name'], 'form-' );

        if ( $candidate_is_canonical !== $current_is_canonical ) {
            return $candidate_is_canonical;
        }

        if ( $candidate['order_weight'] !== $current['order_weight'] ) {
            return $candidate['order_weight'] < $current['order_weight'];
        }

        return $candidate['id'] > $current['id'];
    }

    // ─── [mc_company_portals] ────────────────────────────────────────────────

    public function shortcode_company_portals( $atts ): string {
        $portals = [
            [
                'slug'        => 'anstra',
                'name'        => 'Projection Anstra',
                'desc'        => 'Gestión administrativa, contabilidad y recursos humanos del grupo corporativo.',
                'color_start' => '#1A2E52',
                'color_end'   => '#253E6E',
                'link_color'  => '#1A2E52',
                'header_bg_color'   => '#FFFFFF',
                'header_text_color' => '#0F172A',
                'url'         => home_url( '/anstra/' ),
                'tags'        => [ 'RRHH', 'Contabilidad', 'Administración' ],
                'count_label' => '4 formularios',
            ],
            [
                'slug'        => 'essenza',
                'name'        => 'Essenza Foods',
                'desc'        => 'Gestión de marca, comercial, mercadeo y ventas de la línea de alimentos.',
                'color_start' => '#1B6B45',
                'color_end'   => '#237D53',
                'link_color'  => '#1B6B45',
                'header_bg_color'   => '#FFFFFF',
                'header_text_color' => '#0F172A',
                'url'         => home_url( '/essenza/' ),
                'tags'        => [ 'RRHH', 'Comercial', 'Mercadeo' ],
                'count_label' => '4 formularios',
            ],
            [
                'slug'        => 'budefry',
                'name'        => 'Budefry SAS',
                'desc'        => 'Operación, logística y procesos de producción industrial.',
                'color_start' => '#2D3748',
                'color_end'   => '#3D4F66',
                'link_color'  => '#2D3748',
                'header_bg_color'   => '#FFFFFF',
                'header_text_color' => '#0F172A',
                'url'         => home_url( '/budefry/' ),
                'tags'        => [ 'RRHH', 'Producción', 'Operaciones' ],
                'count_label' => '4 formularios',
            ],
            [
                'slug'        => 'interactua',
                'name'        => 'Interactúa',
                'desc'        => 'Cultura corporativa, employer branding, reconocimientos y eventos del grupo.',
                'color_start' => '#4338CA',
                'color_end'   => '#5048D6',
                'link_color'  => '#4338CA',
                'header_bg_color'   => '#FFFFFF',
                'header_text_color' => '#0F172A',
                'url'         => home_url( '/interactua/' ),
                'tags'        => [ 'Reconocimientos', 'Eventos', 'Cultura' ],
                'count_label' => 'Novedades',
            ],
        ];

        if ( class_exists( 'MC_Intranet_Branding_Settings' ) ) {
            foreach ( $portals as &$portal ) {
                $portal_slug = sanitize_key( (string) ( $portal['slug'] ?? '' ) );

                if ( ! in_array( $portal_slug, MC_Intranet_Branding_Settings::editable_companies(), true ) ) {
                    continue;
                }

                $branding_settings = MC_Intranet_Branding_Settings::get_company_settings( $portal_slug );

                if ( ! empty( $branding_settings['name'] ) ) {
                    $portal['name'] = (string) $branding_settings['name'];
                }

                if ( ! empty( $branding_settings['header_bg_color'] ) ) {
                    $portal['header_bg_color'] = (string) $branding_settings['header_bg_color'];
                }

                if ( ! empty( $branding_settings['header_text_color'] ) ) {
                    $portal['header_text_color'] = (string) $branding_settings['header_text_color'];
                }
            }
            unset( $portal );
        }

        ob_start();
        echo '<div class="company-portals-grid">';
        foreach ( $portals as $portal ) {
            include MC_CORE_TEMPLATES . 'company-portal-card.php';
        }
        echo '</div>';
        return ob_get_clean();
    }

    // ─── [mc_sedes] ──────────────────────────────────────────────────────────

    public function shortcode_sedes( $atts ): string {
        $atts    = shortcode_atts( [ 'empresa' => '' ], $atts, 'mc_sedes' );
        $empresa = sanitize_key( $atts['empresa'] );

        $args = [
            'post_type'      => 'mc_sede',
            'posts_per_page' => 20,
            'post_status'    => 'publish',
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
        ];

        if ( $empresa ) {
            $args['tax_query'] = [ [
                'taxonomy' => 'mc_empresa',
                'field'    => 'slug',
                'terms'    => $empresa,
            ] ];
        }

        $query = new WP_Query( $args );
        if ( ! $query->have_posts() ) {
            return '';
        }

        ob_start();
        echo '<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem;">';
        while ( $query->have_posts() ) {
            $query->the_post();
            $sede_data = [
                'company'  => esc_html( (string) get_post_meta( get_the_ID(), 'company_label', true ) ),
                'name'     => esc_html( get_the_title() ),
                'address'  => esc_html( (string) get_post_meta( get_the_ID(), 'address_full', true ) ),
                'maps_url' => esc_url( (string) get_post_meta( get_the_ID(), 'maps_url', true ) ),
                'logo_id'  => absint( (string) get_post_meta( get_the_ID(), 'sede_logo_id', true ) ),
            ];
            include MC_CORE_TEMPLATES . 'footer-location.php';
        }
        echo '</div>';
        wp_reset_postdata();
        return ob_get_clean();
    }

    // ─── [mc_reconocimientos] ────────────────────────────────────────────────

    public function shortcode_reconocimientos( $atts ): string {
        $atts  = shortcode_atts( [ 'limit' => 10 ], $atts, 'mc_reconocimientos' );
        $limit = absint( $atts['limit'] );

        $query = new WP_Query( [
            'post_type'      => 'mc_reconocimiento',
            'posts_per_page' => $limit,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
        ] );

        if ( ! $query->have_posts() ) {
            return '';
        }

        ob_start();
        echo '<div class="recognition-grid">';
        while ( $query->have_posts() ) {
            $query->the_post();
            $thumbnail_id = get_post_thumbnail_id( get_the_ID() );
            $rec_data = [
                'name'      => esc_html( get_the_title() ),
                'desc'      => wp_kses_post( get_the_content() ),
                'image_url' => (string) get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' ),
                'image_alt' => $thumbnail_id ? (string) get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true ) : '',
            ];
            include MC_CORE_TEMPLATES . 'recognition-card.php';
        }
        echo '</div>';
        wp_reset_postdata();
        return ob_get_clean();
    }

    // ─── [mc_eventos] ────────────────────────────────────────────────────────

    public function shortcode_eventos( $atts ): string {
        $atts     = shortcode_atts( [ 'limit' => 10, 'upcoming' => '' ], $atts, 'mc_eventos' );
        $limit    = absint( $atts['limit'] );
        $upcoming = 'yes' === sanitize_key( $atts['upcoming'] );

        $args = [
            'post_type'      => 'mc_evento',
            'posts_per_page' => $limit,
            'post_status'    => 'publish',
            'meta_key'       => 'event_date',
            'orderby'        => 'meta_value',
            'order'          => 'DESC',
        ];

        if ( $upcoming ) {
            $args['meta_query'] = [ [
                'key'     => 'event_date',
                'value'   => gmdate( 'Y-m-d' ),
                'compare' => '>=',
                'type'    => 'DATE',
            ] ];
            $args['order'] = 'ASC';
        }

        $query = new WP_Query( $args );
        if ( ! $query->have_posts() ) {
            return '';
        }

        ob_start();
        echo '<div class="events-timeline">';
        while ( $query->have_posts() ) {
            $query->the_post();
            $thumbnail_id = get_post_thumbnail_id( get_the_ID() );
            $gallery_ids = array_values( array_filter( array_map( 'absint', explode( ',', (string) get_post_meta( get_the_ID(), 'event_gallery_ids', true ) ) ) ) );
            $gallery = [];

            foreach ( $gallery_ids as $attachment_id ) {
                $gallery_url = wp_get_attachment_image_url( $attachment_id, 'medium_large' );

                if ( ! $gallery_url ) {
                    continue;
                }

                $gallery[] = [
                    'url' => (string) $gallery_url,
                    'alt' => (string) get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ),
                ];
            }

            if ( [] === $gallery ) {
                $fallback_image_url = (string) get_the_post_thumbnail_url( get_the_ID(), 'medium_large' );

                if ( $fallback_image_url ) {
                    $gallery[] = [
                        'url' => $fallback_image_url,
                        'alt' => $thumbnail_id ? (string) get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true ) : '',
                    ];
                }
            }

            $event_data = [
                'title'    => esc_html( get_the_title() ),
                'date'     => esc_html( (string) get_post_meta( get_the_ID(), 'event_date', true ) ),
                'mode'     => esc_html( (string) get_post_meta( get_the_ID(), 'event_mode', true ) ),
                'location' => esc_html( (string) get_post_meta( get_the_ID(), 'event_location', true ) ),
                'content'  => wp_kses_post( get_the_content() ),
                'featured' => (bool) get_post_meta( get_the_ID(), 'event_featured', true ),
                'gallery'  => $gallery,
            ];
            include MC_CORE_TEMPLATES . 'event-item.php';
        }
        echo '</div>';
        wp_reset_postdata();
        return ob_get_clean();
    }

    // ─── [mc_context_alert] ──────────────────────────────────────────────────

    public function shortcode_context_alert( $atts ): string {
        $atts    = shortcode_atts( [ 'empresa' => '' ], $atts, 'mc_context_alert' );
        $empresa = sanitize_key( $atts['empresa'] );

        if ( ! $empresa ) {
            $empresa = MC_Intranet_Company_Context::get_context();
        }

        $config = [
            'anstra' => [
                'label'      => 'Projection Anstra',
                'alert_class' => 'alert--info',
                'title'      => 'Portal exclusivo de Projection Anstra',
                'body'       => 'Los formularios de esta sección corresponden únicamente a colaboradores de Projection Anstra. Si perteneces a otra empresa del grupo, regresa al %s y selecciona tu empresa.',
            ],
            'essenza' => [
                'label'      => 'Essenza Foods',
                'alert_class' => 'alert--info',
                'title'      => 'Portal exclusivo de Essenza Foods',
                'body'       => 'Los formularios de esta sección corresponden únicamente a colaboradores de Essenza Foods (NIT 901 971 854-7). Si perteneces a otra empresa del grupo, regresa al %s.',
            ],
            'budefry' => [
                'label'      => 'Budefry SAS',
                'alert_class' => 'alert--warning',
                'title'      => 'Portal exclusivo de Budefry SAS',
                'body'       => 'Los formularios de esta sección corresponden únicamente a colaboradores de Budefry SAS (NIT 901 565 887-9). Si operas desde planta en Guarne, usa tu dispositivo móvil. Si perteneces a otra empresa, regresa al %s.',
            ],
            'interactua' => [
                'label'      => 'Interactúa',
                'alert_class' => 'alert--info',
                'title'      => 'Portal exclusivo de Interactúa',
                'body'       => 'El contenido de esta sección corresponde a cultura corporativa y reconocimientos del grupo. Para formularios empresariales, regresa al %s.',
            ],
        ];

        if ( ! isset( $config[ $empresa ] ) ) {
            return '';
        }

        $label             = $config[ $empresa ]['label'];
        $alert_class       = $config[ $empresa ]['alert_class'];
        $alert_title       = $config[ $empresa ]['title'];
        $alert_body_format = $config[ $empresa ]['body'];
        $portal_link       = '<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'portal principal', 'mc-intranet-core' ) . '</a>';
        $alert_body        = sprintf( $alert_body_format, $portal_link );

        $icon_markup = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>';
        if ( 'budefry' === $empresa ) {
            $icon_markup = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m10.29 3.86-8.35 14.5A2 2 0 0 0 3.67 21h16.66a2 2 0 0 0 1.73-2.64l-8.35-14.5a2 2 0 0 0-3.42 0Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>';
        }

        ob_start();
        ?>
        <section class="context-alert-block" aria-label="<?php echo esc_attr( $label ); ?>">
            <div class="container">
                <div class="alert <?php echo esc_attr( $alert_class ); ?>" role="alert">
                    <?php echo $icon_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <div>
                        <p class="alert__title"><?php echo esc_html( $alert_title ); ?></p>
                        <p class="alert__body"><?php echo wp_kses_post( $alert_body ); ?></p>
                    </div>
                </div>
            </div>
        </section>
        <?php
        return ob_get_clean();
    }

    // ─── [mc_directorio_contactos] ───────────────────────────────────────────

    /**
     * Muestra el directorio corporativo de contactos, agrupado por área.
     *
     * @param array $atts {
     *   @type string $empresa  Slug de empresa: mc|anstra|essenza|budefry|interactua. Default: ''.
     * }
     */
    public function shortcode_directorio_contactos( $atts ): string {
        $atts = shortcode_atts( [
            'empresa' => '',
        ], $atts, 'mc_directorio_contactos' );

        $empresa = sanitize_key( $atts['empresa'] );

        $meta_query = [ 'relation' => 'AND' ];

        if ( $empresa ) {
            $meta_query[] = [
                'key'     => 'company_context',
                'value'   => $empresa,
                'compare' => '=',
            ];
        }

        $query = new WP_Query( [
            'post_type'      => 'mc_directorio',
            'posts_per_page' => 200,
            'post_status'    => 'publish',
            'meta_query'     => $meta_query,
            'orderby'        => 'title',
            'order'          => 'ASC',
        ] );

        if ( ! $query->have_posts() ) {
            return '';
        }

        // Agrupar por área y preparar listado plano para render único con tabs.
        $groups       = [];
        $all_contacts = [];

        while ( $query->have_posts() ) {
            $query->the_post();

            $contact_id = get_the_ID();
            $contact    = [
                'id'      => $contact_id,
                'nombre'  => (string) get_post_meta( $contact_id, 'nombre', true ) ?: get_the_title(),
                'cargo'   => (string) get_post_meta( $contact_id, 'cargo', true ),
                'area'    => (string) get_post_meta( $contact_id, 'area', true ),
                'celular' => (string) get_post_meta( $contact_id, 'celular', true ),
                'email'   => (string) get_post_meta( $contact_id, 'email', true ),
                'empresa' => (string) get_post_meta( $contact_id, 'company_context', true ),
            ];

            $area_key = $contact['area'] ?: __( 'Sin área', 'mc-intranet-core' );
            $groups[ $area_key ][] = $contact;
            $all_contacts[] = $contact;
        }
        wp_reset_postdata();

        if ( empty( $groups ) ) {
            return '';
        }

        ksort( $groups );

        uasort(
            $all_contacts,
            static function ( array $a, array $b ): int {
                $area_compare = strcmp( (string) $a['area'], (string) $b['area'] );
                if ( 0 !== $area_compare ) {
                    return $area_compare;
                }

                return strcmp( (string) $a['nombre'], (string) $b['nombre'] );
            }
        );

        ob_start();
        include MC_CORE_TEMPLATES . 'contact-directory-tabs.php';

        return ob_get_clean();
    }

    // ─── [mc_login_screen] ──────────────────────────────────────────────────

    public function shortcode_login_screen( $atts ): string {
        $atts = shortcode_atts( [
            'title'    => __( 'Ingresa a la intranet', 'mc-intranet-core' ),
            'subtitle' => __( 'Autentícate con tu usuario corporativo para acceder al contenido interno.', 'mc-intranet-core' ),
        ], $atts, 'mc_login_screen' );

        $redirect_to = MC_Intranet_Access_Control::get_requested_redirect_target();
        $message_key = isset( $_GET['mc_login'] ) ? sanitize_key( wp_unslash( $_GET['mc_login'] ) ) : '';
        $messages    = [
            'failed'    => __( 'No fue posible iniciar sesión con esas credenciales. Verifica tu usuario y contraseña.', 'mc-intranet-core' ),
            'loggedout' => __( 'Tu sesión fue cerrada correctamente.', 'mc-intranet-core' ),
        ];

        if ( is_user_logged_in() ) {
            if ( current_user_can( MC_Intranet_Access_Control::ACCESS_CAPABILITY ) ) {
                return sprintf(
                    '<div class="mc-auth-state"><p>%1$s</p><p><a class="button" href="%2$s">%3$s</a></p></div>',
                    esc_html__( 'Ya tienes una sesión activa con acceso a la intranet.', 'mc-intranet-core' ),
                    esc_url( $redirect_to ?: home_url( '/' ) ),
                    esc_html__( 'Continuar', 'mc-intranet-core' )
                );
            }

            return do_shortcode( '[mc_access_denied]' );
        }

        ob_start();
        ?>
        <section class="mc-auth-shell mc-auth-shell--login">
            <div class="mc-auth-card">
                <p class="mc-auth-shell__eyebrow"><?php esc_html_e( 'MC Intranet', 'mc-intranet-core' ); ?></p>
                <h1 class="mc-auth-shell__title"><?php echo esc_html( $atts['title'] ); ?></h1>
                <p class="mc-auth-shell__subtitle"><?php echo esc_html( $atts['subtitle'] ); ?></p>

                <?php if ( isset( $messages[ $message_key ] ) ) : ?>
                    <div class="alert alert--warning" role="alert">
                        <div>
                            <p class="alert__title"><?php esc_html_e( 'Atención', 'mc-intranet-core' ); ?></p>
                            <p class="alert__body"><?php echo esc_html( $messages[ $message_key ] ); ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="mc-auth-form">
                    <?php
                    wp_login_form( [
                        'echo'           => true,
                        'remember'       => true,
                        'redirect'       => $redirect_to ?: home_url( '/' ),
                        'label_username' => __( 'Correo o usuario', 'mc-intranet-core' ),
                        'label_password' => __( 'Contraseña', 'mc-intranet-core' ),
                        'label_remember' => __( 'Mantener mi sesión', 'mc-intranet-core' ),
                        'label_log_in'   => __( 'Ingresar', 'mc-intranet-core' ),
                    ] );
                    ?>
                </div>

                <p class="mc-auth-shell__meta">
                    <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( '¿Olvidaste tu contraseña?', 'mc-intranet-core' ); ?></a>
                </p>
            </div>
        </section>
        <?php

        return ob_get_clean();
    }

    // ─── [display-posts] ────────────────────────────────────────────────────

    /**
     * @param array $atts {
     *   @type string $subtitle       Subtítulo/encabezado de la sección. Default: 'Boletines'.
     *   @type int    $posts_per_page Número de posts a mostrar. Default: 40.
     *   @type string $empresa        Slug de empresa (reservado para filtrado futuro). Default: 'interactua'.
     * }
     */
    public function shortcode_display_posts( $atts ): string {
        $atts = shortcode_atts( [
            'subtitle'       => 'Boletines',
            'posts_per_page' => 40,
            'empresa'        => 'interactua',
        ], $atts, 'display-posts' );

        $subtitle       = sanitize_text_field( $atts['subtitle'] );
        $posts_per_page = absint( $atts['posts_per_page'] );
        $empresa        = sanitize_key( $atts['empresa'] );

        $query = new WP_Query( [
            'post_type'      => 'post',
            'posts_per_page' => $posts_per_page,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
        ] );

        if ( ! $query->have_posts() ) {
            return '';
        }

        $posts = [];

        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id      = get_the_ID();
            $thumbnail_id = get_post_thumbnail_id( $post_id );
            $raw_cats     = get_the_category( $post_id );

            $categories = [];
            foreach ( $raw_cats as $cat ) {
                $categories[] = [ 'name' => $cat->name, 'slug' => $cat->slug ];
            }

            $posts[] = [
                'id'          => $post_id,
                'title'       => get_the_title(),
                'excerpt'     => wp_trim_words( get_the_excerpt(), 25 ),
                'permalink'   => get_permalink(),
                'date'        => get_the_date( 'd M Y' ),
                'date_iso'    => get_the_date( 'c' ),
                'image_url'   => (string) get_the_post_thumbnail_url( $post_id, 'medium_large' ),
                'image_alt'   => $thumbnail_id
                                    ? (string) get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true )
                                    : get_the_title(),
                'categories'  => $categories,
                'search_text' => implode( ' ', [
                    get_the_title(),
                    get_the_excerpt(),
                    implode( ' ', wp_list_pluck( $raw_cats, 'name' ) ),
                ] ),
            ];
        }
        wp_reset_postdata();

        if ( empty( $posts ) ) {
            return '';
        }

        ob_start();
        include MC_CORE_TEMPLATES . 'display-posts.php';
        return ob_get_clean();
    }

    // ─── [mc_access_denied] ─────────────────────────────────────────────────

    public function shortcode_access_denied( $atts ): string {
        $atts = shortcode_atts( [
            'title' => __( 'Acceso denegado', 'mc-intranet-core' ),
            'body'  => __( 'Tu usuario inició sesión correctamente, pero aún no tiene habilitado el acceso a la intranet. Solicita autorización al equipo administrador.', 'mc-intranet-core' ),
        ], $atts, 'mc_access_denied' );

        $logout_url    = wp_logout_url( add_query_arg( 'mc_login', 'loggedout', MC_Intranet_Access_Control::get_login_page_url() ) );
        $support_email = sanitize_email( (string) get_option( 'admin_email' ) );

        ob_start();
        ?>
        <section class="mc-auth-shell mc-auth-shell--denied">
            <div class="mc-auth-card">
                <p class="mc-auth-shell__eyebrow"><?php esc_html_e( 'MC Intranet', 'mc-intranet-core' ); ?></p>
                <h1 class="mc-auth-shell__title"><?php echo esc_html( $atts['title'] ); ?></h1>
                <p class="mc-auth-shell__subtitle"><?php echo esc_html( $atts['body'] ); ?></p>

                <div class="mc-auth-actions">
                    <a class="button" href="mailto:<?php echo esc_attr( $support_email ); ?>"><?php esc_html_e( 'Solicitar acceso', 'mc-intranet-core' ); ?></a>
                    <?php if ( is_user_logged_in() ) : ?>
                        <a class="button button-secondary" href="<?php echo esc_url( $logout_url ); ?>"><?php esc_html_e( 'Cerrar sesión', 'mc-intranet-core' ); ?></a>
                    <?php else : ?>
                        <a class="button button-secondary" href="<?php echo esc_url( MC_Intranet_Access_Control::get_login_page_url() ); ?>"><?php esc_html_e( 'Volver al login', 'mc-intranet-core' ); ?></a>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        <?php

        return ob_get_clean();
    }
}
