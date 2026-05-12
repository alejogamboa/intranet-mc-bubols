<?php
/**
 * Ajustes de branding por empresa para portales.
 *
 * @package MC_Intranet_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MC_Intranet_Branding_Settings {

    public const OPTION_KEY = 'mc_intranet_company_branding';

    /**
     * Empresas soportadas en esta pantalla.
     *
     * @return string[]
     */
    public static function editable_companies(): array {
        return [ 'anstra', 'essenza', 'budefry', 'interactua' ];
    }

    public function __construct() {
        add_action( 'admin_menu', [ $this, 'register_menu' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
    }

    public function register_menu(): void {
        add_options_page(
            __( 'Branding de Empresas', 'mc-intranet-core' ),
            __( 'Branding Empresas', 'mc-intranet-core' ),
            'manage_options',
            'mc-intranet-branding',
            [ $this, 'render_page' ]
        );
    }

    public function register_settings(): void {
        register_setting(
            'mc_intranet_branding_group',
            self::OPTION_KEY,
            [
                'type'              => 'array',
                'sanitize_callback' => [ $this, 'sanitize_settings' ],
                'default'           => self::get_default_settings(),
            ]
        );
    }

    public function enqueue_admin_assets( string $hook_suffix ): void {
        if ( 'settings_page_mc-intranet-branding' !== $hook_suffix ) {
            return;
        }

        wp_enqueue_media();
    }

    /**
     * @param mixed $input Valor crudo del option.
     * @return array<string,array<string,mixed>>
     */
    public function sanitize_settings( $input ): array {
        $defaults = self::get_default_settings();
        $input    = is_array( $input ) ? $input : [];
        $clean    = [];

        foreach ( self::editable_companies() as $company ) {
            $raw = isset( $input[ $company ] ) && is_array( $input[ $company ] ) ? $input[ $company ] : [];

            $name = isset( $raw['name'] ) ? sanitize_text_field( (string) $raw['name'] ) : '';
            if ( '' === $name ) {
                $name = $defaults[ $company ]['name'];
            }

            $header_bg_color = sanitize_hex_color( isset( $raw['header_bg_color'] ) ? (string) $raw['header_bg_color'] : '' );
            if ( ! $header_bg_color ) {
                $header_bg_color = $defaults[ $company ]['header_bg_color'];
            }

            $header_text_color = sanitize_hex_color( isset( $raw['header_text_color'] ) ? (string) $raw['header_text_color'] : '' );
            if ( ! $header_text_color ) {
                $header_text_color = $defaults[ $company ]['header_text_color'];
            }

            $hero_eyebrow = isset( $raw['hero_eyebrow'] ) ? sanitize_text_field( (string) $raw['hero_eyebrow'] ) : '';
            if ( '' === $hero_eyebrow ) {
                $hero_eyebrow = $defaults[ $company ]['hero_eyebrow'];
            }

            $hero_title_line_1 = isset( $raw['hero_title_line_1'] ) ? sanitize_text_field( (string) $raw['hero_title_line_1'] ) : '';
            if ( '' === $hero_title_line_1 ) {
                $hero_title_line_1 = $defaults[ $company ]['hero_title_line_1'];
            }

            $hero_title_line_2 = isset( $raw['hero_title_line_2'] ) ? sanitize_text_field( (string) $raw['hero_title_line_2'] ) : '';

            $hero_description = isset( $raw['hero_description'] ) ? sanitize_textarea_field( (string) $raw['hero_description'] ) : '';
            if ( '' === $hero_description ) {
                $hero_description = $defaults[ $company ]['hero_description'];
            }

            $hero_bg_color = sanitize_hex_color( isset( $raw['hero_bg_color'] ) ? (string) $raw['hero_bg_color'] : '' );
            if ( ! $hero_bg_color ) {
                $hero_bg_color = $defaults[ $company ]['hero_bg_color'];
            }

            $clean[ $company ] = [
                'name'              => $name,
                'header_bg_color'   => $header_bg_color,
                'header_text_color' => $header_text_color,
                'logo_id'           => absint( $raw['logo_id'] ?? 0 ),
                'hero_eyebrow'      => $hero_eyebrow,
                'hero_title_line_1' => $hero_title_line_1,
                'hero_title_line_2' => $hero_title_line_2,
                'hero_description'  => $hero_description,
                'hero_bg_color'     => $hero_bg_color,
            ];
        }

        return $clean;
    }

    /**
     * Retorna ajustes de una empresa con fallback a defaults.
     *
     * @param string $company Slug de empresa.
     * @return array<string,mixed>
     */
    public static function get_company_settings( string $company ): array {
        $slug     = sanitize_key( $company );
        $defaults = self::get_default_settings();

        if ( ! isset( $defaults[ $slug ] ) ) {
            return [];
        }

        $stored = get_option( self::OPTION_KEY, [] );
        $stored = is_array( $stored ) && isset( $stored[ $slug ] ) && is_array( $stored[ $slug ] ) ? $stored[ $slug ] : [];

        return wp_parse_args( $stored, $defaults[ $slug ] );
    }

    /**
     * @return array<string,array<string,mixed>>
     */
    public static function get_default_settings(): array {
        return [
            'anstra'  => [
                'name'              => 'Projection Anstra',
                'header_bg_color'   => '#FFFFFF',
                'header_text_color' => '#0F172A',
                'logo_id'           => 0,
                'hero_eyebrow'      => 'Portal Empresarial · NIT 901 967 530-0',
                'hero_title_line_1' => 'Projection',
                'hero_title_line_2' => 'Anstra',
                'hero_description'  => 'Portal de gestión administrativa, contabilidad y recursos humanos. Accede a todos los formularios y documentos internos de la empresa.',
                'hero_bg_color'     => '#1A2E52',
            ],
            'essenza' => [
                'name'              => 'Essenza Foods',
                'header_bg_color'   => '#FFFFFF',
                'header_text_color' => '#0F172A',
                'logo_id'           => 0,
                'hero_eyebrow'      => 'Portal Empresarial · NIT 901 971 854-7',
                'hero_title_line_1' => 'Essenza',
                'hero_title_line_2' => 'Foods',
                'hero_description'  => 'Portal de gestión comercial, mercadeo, marca y recursos humanos de Essenza Foods. Accede a todos tus formularios y documentos internos.',
                'hero_bg_color'     => '#1B6B45',
            ],
            'budefry' => [
                'name'              => 'Budefry SAS',
                'header_bg_color'   => '#FFFFFF',
                'header_text_color' => '#0F172A',
                'logo_id'           => 0,
                'hero_eyebrow'      => 'Portal Empresarial · NIT 901 565 887-9',
                'hero_title_line_1' => 'Budefry',
                'hero_title_line_2' => 'SAS',
                'hero_description'  => 'Portal de operación, logística y producción industrial. Accede a los formularios de recursos humanos y documentos de gestión de planta.',
                'hero_bg_color'     => '#2D3748',
            ],
            'interactua' => [
                'name'              => 'Interactúa',
                'header_bg_color'   => '#FFFFFF',
                'header_text_color' => '#0F172A',
                'logo_id'           => 0,
                'hero_eyebrow'      => 'Interactúa · Cultura Corporativa',
                'hero_title_line_1' => 'Interactúa',
                'hero_title_line_2' => '',
                'hero_description'  => 'Espacio de cultura corporativa, reconocimientos y eventos importantes del grupo MC.',
                'hero_bg_color'     => '#4338CA',
            ],
        ];
    }

    public function render_page(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $defaults = self::get_default_settings();
        $options  = get_option( self::OPTION_KEY, [] );
        $options  = is_array( $options ) ? $options : [];
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Branding de Empresas', 'mc-intranet-core' ); ?></h1>
            <p><?php esc_html_e( 'Edita nombre, colores, logo y contenido del Hero para cada empresa.', 'mc-intranet-core' ); ?></p>

            <form method="post" action="options.php">
                <?php settings_fields( 'mc_intranet_branding_group' ); ?>

                <?php foreach ( self::editable_companies() as $company ) : ?>
                    <?php
                    $company_data = isset( $options[ $company ] ) && is_array( $options[ $company ] ) ? $options[ $company ] : [];
                    $company_data = wp_parse_args( $company_data, $defaults[ $company ] );
                    $logo_id      = absint( $company_data['logo_id'] );
                    ?>
                    <table class="form-table" role="presentation" style="max-width:760px;background:#fff;border:1px solid #dcdcde;padding:18px 20px;margin:0 0 20px;">
                        <tbody>
                            <tr>
                                <th scope="row" colspan="2" style="padding-bottom:2px;">
                                    <h2 style="margin:0;font-size:18px;"><?php echo esc_html( $defaults[ $company ]['name'] ); ?></h2>
                                </th>
                            </tr>
                            <tr>
                                <th scope="row"><label for="mc-branding-<?php echo esc_attr( $company ); ?>-name"><?php esc_html_e( 'Nombre del encabezado', 'mc-intranet-core' ); ?></label></th>
                                <td>
                                    <input
                                        type="text"
                                        id="mc-branding-<?php echo esc_attr( $company ); ?>-name"
                                        name="<?php echo esc_attr( self::OPTION_KEY ); ?>[<?php echo esc_attr( $company ); ?>][name]"
                                        value="<?php echo esc_attr( (string) $company_data['name'] ); ?>"
                                        class="regular-text"
                                    >
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="mc-branding-<?php echo esc_attr( $company ); ?>-hero-eyebrow"><?php esc_html_e( 'Hero: texto superior', 'mc-intranet-core' ); ?></label></th>
                                <td>
                                    <input
                                        type="text"
                                        id="mc-branding-<?php echo esc_attr( $company ); ?>-hero-eyebrow"
                                        name="<?php echo esc_attr( self::OPTION_KEY ); ?>[<?php echo esc_attr( $company ); ?>][hero_eyebrow]"
                                        value="<?php echo esc_attr( (string) $company_data['hero_eyebrow'] ); ?>"
                                        class="regular-text"
                                    >
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="mc-branding-<?php echo esc_attr( $company ); ?>-hero-title-1"><?php esc_html_e( 'Hero: título línea 1', 'mc-intranet-core' ); ?></label></th>
                                <td>
                                    <input
                                        type="text"
                                        id="mc-branding-<?php echo esc_attr( $company ); ?>-hero-title-1"
                                        name="<?php echo esc_attr( self::OPTION_KEY ); ?>[<?php echo esc_attr( $company ); ?>][hero_title_line_1]"
                                        value="<?php echo esc_attr( (string) $company_data['hero_title_line_1'] ); ?>"
                                        class="regular-text"
                                    >
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="mc-branding-<?php echo esc_attr( $company ); ?>-hero-title-2"><?php esc_html_e( 'Hero: título línea 2 (opcional)', 'mc-intranet-core' ); ?></label></th>
                                <td>
                                    <input
                                        type="text"
                                        id="mc-branding-<?php echo esc_attr( $company ); ?>-hero-title-2"
                                        name="<?php echo esc_attr( self::OPTION_KEY ); ?>[<?php echo esc_attr( $company ); ?>][hero_title_line_2]"
                                        value="<?php echo esc_attr( (string) $company_data['hero_title_line_2'] ); ?>"
                                        class="regular-text"
                                    >
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="mc-branding-<?php echo esc_attr( $company ); ?>-hero-description"><?php esc_html_e( 'Hero: descripción', 'mc-intranet-core' ); ?></label></th>
                                <td>
                                    <textarea
                                        id="mc-branding-<?php echo esc_attr( $company ); ?>-hero-description"
                                        name="<?php echo esc_attr( self::OPTION_KEY ); ?>[<?php echo esc_attr( $company ); ?>][hero_description]"
                                        class="large-text"
                                        rows="3"
                                    ><?php echo esc_textarea( (string) $company_data['hero_description'] ); ?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="mc-branding-<?php echo esc_attr( $company ); ?>-hero-bg"><?php esc_html_e( 'Hero: color de fondo', 'mc-intranet-core' ); ?></label></th>
                                <td>
                                    <input
                                        type="color"
                                        id="mc-branding-<?php echo esc_attr( $company ); ?>-hero-bg"
                                        name="<?php echo esc_attr( self::OPTION_KEY ); ?>[<?php echo esc_attr( $company ); ?>][hero_bg_color]"
                                        value="<?php echo esc_attr( (string) ( $company_data['hero_bg_color'] ?? '#1A2E52' ) ); ?>"
                                    >
                                    <p class="description"><?php esc_html_e( 'Color de fondo de la sección Hero en la página de esta empresa.', 'mc-intranet-core' ); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="mc-branding-<?php echo esc_attr( $company ); ?>-bg"><?php esc_html_e( 'Color de fondo del encabezado', 'mc-intranet-core' ); ?></label></th>
                                <td>
                                    <input
                                        type="color"
                                        id="mc-branding-<?php echo esc_attr( $company ); ?>-bg"
                                        name="<?php echo esc_attr( self::OPTION_KEY ); ?>[<?php echo esc_attr( $company ); ?>][header_bg_color]"
                                        value="<?php echo esc_attr( (string) $company_data['header_bg_color'] ); ?>"
                                    >
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="mc-branding-<?php echo esc_attr( $company ); ?>-text"><?php esc_html_e( 'Color del texto del encabezado', 'mc-intranet-core' ); ?></label></th>
                                <td>
                                    <input
                                        type="color"
                                        id="mc-branding-<?php echo esc_attr( $company ); ?>-text"
                                        name="<?php echo esc_attr( self::OPTION_KEY ); ?>[<?php echo esc_attr( $company ); ?>][header_text_color]"
                                        value="<?php echo esc_attr( (string) $company_data['header_text_color'] ); ?>"
                                    >
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php esc_html_e( 'Logo', 'mc-intranet-core' ); ?></th>
                                <td>
                                    <input
                                        type="hidden"
                                        id="mc-branding-<?php echo esc_attr( $company ); ?>-logo-id"
                                        name="<?php echo esc_attr( self::OPTION_KEY ); ?>[<?php echo esc_attr( $company ); ?>][logo_id]"
                                        value="<?php echo esc_attr( (string) $logo_id ); ?>"
                                    >

                                    <div id="mc-branding-<?php echo esc_attr( $company ); ?>-logo-preview" style="margin:0 0 12px;">
                                        <?php if ( $logo_id ) : ?>
                                            <?php echo wp_get_attachment_image( $logo_id, 'medium', false, [ 'style' => 'max-width:240px;height:auto;' ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                        <?php else : ?>
                                            <em><?php esc_html_e( 'Sin logo personalizado (se usa el del tema).', 'mc-intranet-core' ); ?></em>
                                        <?php endif; ?>
                                    </div>

                                    <button
                                        type="button"
                                        class="button mc-branding-upload"
                                        data-target="mc-branding-<?php echo esc_attr( $company ); ?>-logo-id"
                                        data-preview="mc-branding-<?php echo esc_attr( $company ); ?>-logo-preview"
                                    >
                                        <?php esc_html_e( 'Seleccionar logo', 'mc-intranet-core' ); ?>
                                    </button>
                                    <button
                                        type="button"
                                        class="button button-secondary mc-branding-remove"
                                        data-target="mc-branding-<?php echo esc_attr( $company ); ?>-logo-id"
                                        data-preview="mc-branding-<?php echo esc_attr( $company ); ?>-logo-preview"
                                        style="margin-left:8px;"
                                    >
                                        <?php esc_html_e( 'Quitar logo', 'mc-intranet-core' ); ?>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                <?php endforeach; ?>

                <?php submit_button( __( 'Guardar cambios', 'mc-intranet-core' ) ); ?>
            </form>
        </div>

        <script>
            (function() {
                const uploadButtons = document.querySelectorAll('.mc-branding-upload');
                const removeButtons = document.querySelectorAll('.mc-branding-remove');

                const getPreviewEmptyMarkup = () => '<em><?php echo esc_js( __( 'Sin logo personalizado (se usa el del tema).', 'mc-intranet-core' ) ); ?></em>';

                uploadButtons.forEach((button) => {
                    button.addEventListener('click', () => {
                        const targetId = button.getAttribute('data-target');
                        const previewId = button.getAttribute('data-preview');
                        const target = document.getElementById(targetId);
                        const preview = document.getElementById(previewId);

                        if (!target || !preview || typeof wp === 'undefined' || !wp.media) {
                            return;
                        }

                        const frame = wp.media({
                            title: '<?php echo esc_js( __( 'Seleccionar logo', 'mc-intranet-core' ) ); ?>',
                            button: {
                                text: '<?php echo esc_js( __( 'Usar este logo', 'mc-intranet-core' ) ); ?>'
                            },
                            library: {
                                type: 'image'
                            },
                            multiple: false
                        });

                        frame.on('select', () => {
                            const attachment = frame.state().get('selection').first().toJSON();
                            target.value = String(attachment.id || '');

                            const imageUrl = (attachment.sizes && attachment.sizes.medium && attachment.sizes.medium.url)
                                ? attachment.sizes.medium.url
                                : attachment.url;

                            if (!imageUrl) {
                                preview.innerHTML = getPreviewEmptyMarkup();
                                return;
                            }

                            preview.innerHTML = '';
                            const image = document.createElement('img');
                            image.src = imageUrl;
                            image.alt = '';
                            image.style.maxWidth = '240px';
                            image.style.height = 'auto';
                            preview.appendChild(image);
                        });

                        frame.open();
                    });
                });

                removeButtons.forEach((button) => {
                    button.addEventListener('click', () => {
                        const targetId = button.getAttribute('data-target');
                        const previewId = button.getAttribute('data-preview');
                        const target = document.getElementById(targetId);
                        const preview = document.getElementById(previewId);

                        if (!target || !preview) {
                            return;
                        }

                        target.value = '0';
                        preview.innerHTML = getPreviewEmptyMarkup();
                    });
                });
            }());
        </script>
        <?php
    }
}
