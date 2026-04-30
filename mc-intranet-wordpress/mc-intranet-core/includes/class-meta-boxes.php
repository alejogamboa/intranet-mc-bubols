<?php
/**
 * Meta boxes para CPTs de MC Intranet Core.
 *
 * @package MC_Intranet_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MC_Intranet_Meta_Boxes {

    public function __construct() {
        add_action( 'add_meta_boxes', [ $this, 'register_meta_boxes' ] );
        add_action( 'save_post', [ $this, 'save_meta_boxes' ], 10, 2 );
    }

    public function register_meta_boxes(): void {
        add_meta_box(
            'mc_formulario_meta',
            __( 'Datos del Formulario', 'mc-intranet-core' ),
            [ $this, 'render_formulario_meta_box' ],
            'mc_formulario',
            'normal',
            'default'
        );

        add_meta_box(
            'mc_evento_meta',
            __( 'Datos del Evento', 'mc-intranet-core' ),
            [ $this, 'render_evento_meta_box' ],
            'mc_evento',
            'normal',
            'default'
        );

        add_meta_box(
            'mc_reconocimiento_meta',
            __( 'Datos del Reconocimiento', 'mc-intranet-core' ),
            [ $this, 'render_reconocimiento_meta_box' ],
            'mc_reconocimiento',
            'normal',
            'default'
        );

        add_meta_box(
            'mc_sede_meta',
            __( 'Datos de la Sede', 'mc-intranet-core' ),
            [ $this, 'render_sede_meta_box' ],
            'mc_sede',
            'normal',
            'default'
        );

        add_meta_box(
            'mc_directorio_meta',
            __( 'Datos del Contacto', 'mc-intranet-core' ),
            [ $this, 'render_directorio_contactos_meta_box' ],
            'mc_directorio',
            'normal',
            'default'
        );
    }

    public function render_formulario_meta_box( WP_Post $post ): void {
        wp_nonce_field( 'mc_intranet_meta_boxes_action', 'mc_intranet_meta_boxes_nonce' );

        $company_context = (string) get_post_meta( $post->ID, 'company_context', true );
        $area_context    = (string) get_post_meta( $post->ID, 'area_context', true );
        $form_type       = (string) get_post_meta( $post->ID, 'form_type', true );
        $form_url        = (string) get_post_meta( $post->ID, 'form_url', true );
        $cta_label       = (string) get_post_meta( $post->ID, 'cta_label', true );
        $open_new_tab    = (string) get_post_meta( $post->ID, 'open_new_tab', true );
        $is_featured     = (string) get_post_meta( $post->ID, 'is_featured', true );
        $order_weight    = (string) get_post_meta( $post->ID, 'order_weight', true );

        ?>
        <table class="form-table" role="presentation">
            <tr>
                <th><label for="mc_company_context"><?php esc_html_e( 'Company Context', 'mc-intranet-core' ); ?></label></th>
                <td><input type="text" id="mc_company_context" name="mc_company_context" class="regular-text" value="<?php echo esc_attr( $company_context ); ?>" /></td>
            </tr>
            <tr>
                <th><label for="mc_area_context"><?php esc_html_e( 'Area Context', 'mc-intranet-core' ); ?></label></th>
                <td><input type="text" id="mc_area_context" name="mc_area_context" class="regular-text" value="<?php echo esc_attr( $area_context ); ?>" /></td>
            </tr>
            <tr>
                <th><label for="mc_form_type"><?php esc_html_e( 'Form Type', 'mc-intranet-core' ); ?></label></th>
                <td><input type="text" id="mc_form_type" name="mc_form_type" class="regular-text" value="<?php echo esc_attr( $form_type ); ?>" /></td>
            </tr>
            <tr>
                <th><label for="mc_form_url"><?php esc_html_e( 'Form URL', 'mc-intranet-core' ); ?></label></th>
                <td><input type="url" id="mc_form_url" name="mc_form_url" class="regular-text" value="<?php echo esc_attr( $form_url ); ?>" /></td>
            </tr>
            <tr>
                <th><label for="mc_cta_label"><?php esc_html_e( 'CTA Label', 'mc-intranet-core' ); ?></label></th>
                <td><input type="text" id="mc_cta_label" name="mc_cta_label" class="regular-text" value="<?php echo esc_attr( $cta_label ); ?>" /></td>
            </tr>
            <tr>
                <th><?php esc_html_e( 'Open New Tab', 'mc-intranet-core' ); ?></th>
                <td><label><input type="checkbox" name="mc_open_new_tab" value="1" <?php checked( '1', $open_new_tab ); ?> /> <?php esc_html_e( 'Yes', 'mc-intranet-core' ); ?></label></td>
            </tr>
            <tr>
                <th><?php esc_html_e( 'Featured', 'mc-intranet-core' ); ?></th>
                <td><label><input type="checkbox" name="mc_is_featured" value="1" <?php checked( '1', $is_featured ); ?> /> <?php esc_html_e( 'Yes', 'mc-intranet-core' ); ?></label></td>
            </tr>
            <tr>
                <th><label for="mc_order_weight"><?php esc_html_e( 'Order Weight', 'mc-intranet-core' ); ?></label></th>
                <td><input type="number" id="mc_order_weight" name="mc_order_weight" class="small-text" value="<?php echo esc_attr( $order_weight ); ?>" min="0" step="1" /></td>
            </tr>
        </table>
        <?php
    }

    public function render_evento_meta_box( WP_Post $post ): void {
        wp_nonce_field( 'mc_intranet_meta_boxes_action', 'mc_intranet_meta_boxes_nonce' );

        $event_date     = (string) get_post_meta( $post->ID, 'event_date', true );
        $event_mode     = (string) get_post_meta( $post->ID, 'event_mode', true );
        $event_location = (string) get_post_meta( $post->ID, 'event_location', true );
        $event_featured = (string) get_post_meta( $post->ID, 'event_featured', true );
        $company_context = (string) get_post_meta( $post->ID, 'company_context', true );

        ?>
        <table class="form-table" role="presentation">
            <tr>
                <th><label for="mc_event_date"><?php esc_html_e( 'Event Date (YYYY-MM-DD)', 'mc-intranet-core' ); ?></label></th>
                <td><input type="date" id="mc_event_date" name="mc_event_date" value="<?php echo esc_attr( $event_date ); ?>" /></td>
            </tr>
            <tr>
                <th><label for="mc_event_mode"><?php esc_html_e( 'Event Mode', 'mc-intranet-core' ); ?></label></th>
                <td><input type="text" id="mc_event_mode" name="mc_event_mode" class="regular-text" value="<?php echo esc_attr( $event_mode ); ?>" /></td>
            </tr>
            <tr>
                <th><label for="mc_event_location"><?php esc_html_e( 'Event Location', 'mc-intranet-core' ); ?></label></th>
                <td><input type="text" id="mc_event_location" name="mc_event_location" class="regular-text" value="<?php echo esc_attr( $event_location ); ?>" /></td>
            </tr>
            <tr>
                <th><label for="mc_event_company_context"><?php esc_html_e( 'Company Context', 'mc-intranet-core' ); ?></label></th>
                <td><input type="text" id="mc_event_company_context" name="mc_event_company_context" class="regular-text" value="<?php echo esc_attr( $company_context ); ?>" /></td>
            </tr>
            <tr>
                <th><?php esc_html_e( 'Featured Event', 'mc-intranet-core' ); ?></th>
                <td><label><input type="checkbox" name="mc_event_featured" value="1" <?php checked( '1', $event_featured ); ?> /> <?php esc_html_e( 'Yes', 'mc-intranet-core' ); ?></label></td>
            </tr>
        </table>
        <?php
    }

    public function render_reconocimiento_meta_box( WP_Post $post ): void {
        wp_nonce_field( 'mc_intranet_meta_boxes_action', 'mc_intranet_meta_boxes_nonce' );

        $person_role        = (string) get_post_meta( $post->ID, 'person_role', true );
        $person_company     = (string) get_post_meta( $post->ID, 'person_company', true );
        $achievement_type   = (string) get_post_meta( $post->ID, 'achievement_type', true );
        $achievement_excerpt = (string) get_post_meta( $post->ID, 'achievement_excerpt', true );
        $person_initials    = (string) get_post_meta( $post->ID, 'person_initials', true );

        ?>
        <table class="form-table" role="presentation">
            <tr>
                <th><label for="mc_person_role"><?php esc_html_e( 'Person Role', 'mc-intranet-core' ); ?></label></th>
                <td><input type="text" id="mc_person_role" name="mc_person_role" class="regular-text" value="<?php echo esc_attr( $person_role ); ?>" /></td>
            </tr>
            <tr>
                <th><label for="mc_person_company"><?php esc_html_e( 'Person Company', 'mc-intranet-core' ); ?></label></th>
                <td><input type="text" id="mc_person_company" name="mc_person_company" class="regular-text" value="<?php echo esc_attr( $person_company ); ?>" /></td>
            </tr>
            <tr>
                <th><label for="mc_achievement_type"><?php esc_html_e( 'Achievement Type', 'mc-intranet-core' ); ?></label></th>
                <td><input type="text" id="mc_achievement_type" name="mc_achievement_type" class="regular-text" value="<?php echo esc_attr( $achievement_type ); ?>" /></td>
            </tr>
            <tr>
                <th><label for="mc_achievement_excerpt"><?php esc_html_e( 'Achievement Excerpt', 'mc-intranet-core' ); ?></label></th>
                <td><textarea id="mc_achievement_excerpt" name="mc_achievement_excerpt" class="large-text" rows="4"><?php echo esc_textarea( $achievement_excerpt ); ?></textarea></td>
            </tr>
            <tr>
                <th><label for="mc_person_initials"><?php esc_html_e( 'Person Initials', 'mc-intranet-core' ); ?></label></th>
                <td><input type="text" id="mc_person_initials" name="mc_person_initials" class="small-text" maxlength="3" value="<?php echo esc_attr( $person_initials ); ?>" /></td>
            </tr>
        </table>
        <?php
    }

    public function render_sede_meta_box( WP_Post $post ): void {        wp_nonce_field( 'mc_intranet_meta_boxes_action', 'mc_intranet_meta_boxes_nonce' );

        $company_label = (string) get_post_meta( $post->ID, 'company_label', true );
        $address_full  = (string) get_post_meta( $post->ID, 'address_full', true );
        $maps_url      = (string) get_post_meta( $post->ID, 'maps_url', true );

        ?>
        <table class="form-table" role="presentation">
            <tr>
                <th><label for="mc_company_label"><?php esc_html_e( 'Company Label', 'mc-intranet-core' ); ?></label></th>
                <td><input type="text" id="mc_company_label" name="mc_company_label" class="regular-text" value="<?php echo esc_attr( $company_label ); ?>" /></td>
            </tr>
            <tr>
                <th><label for="mc_address_full"><?php esc_html_e( 'Address', 'mc-intranet-core' ); ?></label></th>
                <td><textarea id="mc_address_full" name="mc_address_full" class="large-text" rows="3"><?php echo esc_textarea( $address_full ); ?></textarea></td>
            </tr>
            <tr>
                <th><label for="mc_maps_url"><?php esc_html_e( 'Maps URL', 'mc-intranet-core' ); ?></label></th>
                <td><input type="url" id="mc_maps_url" name="mc_maps_url" class="regular-text" value="<?php echo esc_attr( $maps_url ); ?>" /></td>
            </tr>
        </table>
        <?php
    }

    public function render_directorio_contactos_meta_box( WP_Post $post ): void {
        wp_nonce_field( 'mc_intranet_meta_boxes_action', 'mc_intranet_meta_boxes_nonce' );

        $area            = (string) get_post_meta( $post->ID, 'area', true );
        $cargo           = (string) get_post_meta( $post->ID, 'cargo', true );
        $nombre          = (string) get_post_meta( $post->ID, 'nombre', true );
        $celular         = (string) get_post_meta( $post->ID, 'celular', true );
        $email           = (string) get_post_meta( $post->ID, 'email', true );
        $company_context = (string) get_post_meta( $post->ID, 'company_context', true );

        $allowed_companies = [ 'mc', 'anstra', 'essenza', 'budefry', 'interactua' ];
        ?>
        <table class="form-table" role="presentation">
            <tr>
                <th><label for="mc_dc_company_context"><?php esc_html_e( 'Empresa (Company Context)', 'mc-intranet-core' ); ?></label></th>
                <td>
                    <select id="mc_dc_company_context" name="mc_dc_company_context">
                        <option value=""><?php esc_html_e( '— Seleccionar —', 'mc-intranet-core' ); ?></option>
                        <?php foreach ( $allowed_companies as $slug ) : ?>
                            <option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $company_context, $slug ); ?>><?php echo esc_html( $slug ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="mc_dc_area"><?php esc_html_e( 'Área', 'mc-intranet-core' ); ?></label></th>
                <td><input type="text" id="mc_dc_area" name="mc_dc_area" class="regular-text" value="<?php echo esc_attr( $area ); ?>" /></td>
            </tr>
            <tr>
                <th><label for="mc_dc_cargo"><?php esc_html_e( 'Cargo', 'mc-intranet-core' ); ?></label></th>
                <td><input type="text" id="mc_dc_cargo" name="mc_dc_cargo" class="regular-text" value="<?php echo esc_attr( $cargo ); ?>" /></td>
            </tr>
            <tr>
                <th><label for="mc_dc_nombre"><?php esc_html_e( 'Nombre', 'mc-intranet-core' ); ?></label></th>
                <td><input type="text" id="mc_dc_nombre" name="mc_dc_nombre" class="regular-text" value="<?php echo esc_attr( $nombre ); ?>" /></td>
            </tr>
            <tr>
                <th><label for="mc_dc_celular"><?php esc_html_e( 'Celular', 'mc-intranet-core' ); ?></label></th>
                <td><input type="tel" id="mc_dc_celular" name="mc_dc_celular" class="regular-text" value="<?php echo esc_attr( $celular ); ?>" /></td>
            </tr>
            <tr>
                <th><label for="mc_dc_email"><?php esc_html_e( 'Email', 'mc-intranet-core' ); ?></label></th>
                <td><input type="email" id="mc_dc_email" name="mc_dc_email" class="regular-text" value="<?php echo esc_attr( $email ); ?>" /></td>
            </tr>
        </table>
        <?php
    }

    public function save_meta_boxes( int $post_id, WP_Post $post ): void {
        if ( ! isset( $_POST['mc_intranet_meta_boxes_nonce'] ) ) {
            return;
        }

        $nonce = sanitize_text_field( wp_unslash( $_POST['mc_intranet_meta_boxes_nonce'] ) );
        if ( ! wp_verify_nonce( $nonce, 'mc_intranet_meta_boxes_action' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( wp_is_post_revision( $post_id ) ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        switch ( $post->post_type ) {
            case 'mc_formulario':
                $this->save_text_field( $post_id, 'company_context', 'mc_company_context', 'sanitize_key' );
                $this->save_text_field( $post_id, 'area_context', 'mc_area_context', 'sanitize_key' );
                $this->save_text_field( $post_id, 'form_type', 'mc_form_type', 'sanitize_key' );
                $this->save_text_field( $post_id, 'form_url', 'mc_form_url', 'esc_url_raw' );
                $this->save_text_field( $post_id, 'cta_label', 'mc_cta_label', 'sanitize_text_field' );
                $this->save_checkbox_field( $post_id, 'open_new_tab', 'mc_open_new_tab' );
                $this->save_checkbox_field( $post_id, 'is_featured', 'mc_is_featured' );
                $this->save_number_field( $post_id, 'order_weight', 'mc_order_weight' );
                break;

            case 'mc_evento':
                $this->save_date_field( $post_id, 'event_date', 'mc_event_date' );
                $this->save_text_field( $post_id, 'event_mode', 'mc_event_mode', 'sanitize_key' );
                $this->save_text_field( $post_id, 'event_location', 'mc_event_location', 'sanitize_text_field' );
                $this->save_text_field( $post_id, 'company_context', 'mc_event_company_context', 'sanitize_key' );
                $this->save_checkbox_field( $post_id, 'event_featured', 'mc_event_featured' );
                break;

            case 'mc_reconocimiento':
                $this->save_text_field( $post_id, 'person_role', 'mc_person_role', 'sanitize_text_field' );
                $this->save_text_field( $post_id, 'person_company', 'mc_person_company', 'sanitize_key' );
                $this->save_text_field( $post_id, 'achievement_type', 'mc_achievement_type', 'sanitize_key' );
                $this->save_textarea_field( $post_id, 'achievement_excerpt', 'mc_achievement_excerpt' );
                $this->save_initials_field( $post_id, 'person_initials', 'mc_person_initials' );
                break;

            case 'mc_sede':
                $this->save_text_field( $post_id, 'company_label', 'mc_company_label', 'sanitize_text_field' );
                $this->save_textarea_field( $post_id, 'address_full', 'mc_address_full' );
                $this->save_text_field( $post_id, 'maps_url', 'mc_maps_url', 'esc_url_raw' );
                break;

            case 'mc_directorio':
                $allowed = [ 'mc', 'anstra', 'essenza', 'budefry', 'interactua' ];
                $company = isset( $_POST['mc_dc_company_context'] )
                    ? sanitize_key( wp_unslash( $_POST['mc_dc_company_context'] ) )
                    : '';
                if ( in_array( $company, $allowed, true ) ) {
                    update_post_meta( $post_id, 'company_context', $company );
                } else {
                    delete_post_meta( $post_id, 'company_context' );
                }
                $this->save_text_field( $post_id, 'area',    'mc_dc_area',    'sanitize_text_field' );
                $this->save_text_field( $post_id, 'cargo',   'mc_dc_cargo',   'sanitize_text_field' );
                $this->save_text_field( $post_id, 'nombre',  'mc_dc_nombre',  'sanitize_text_field' );
                $this->save_text_field( $post_id, 'celular', 'mc_dc_celular', 'sanitize_text_field' );
                $this->save_text_field( $post_id, 'email',   'mc_dc_email',   'sanitize_email' );
                break;
        }
    }

    private function save_text_field( int $post_id, string $meta_key, string $post_key, callable $sanitizer ): void {
        if ( ! isset( $_POST[ $post_key ] ) ) {
            return;
        }

        $value = call_user_func( $sanitizer, wp_unslash( $_POST[ $post_key ] ) );

        if ( '' === $value ) {
            delete_post_meta( $post_id, $meta_key );
            return;
        }

        update_post_meta( $post_id, $meta_key, $value );
    }

    private function save_textarea_field( int $post_id, string $meta_key, string $post_key ): void {
        if ( ! isset( $_POST[ $post_key ] ) ) {
            return;
        }

        $value = sanitize_textarea_field( wp_unslash( $_POST[ $post_key ] ) );

        if ( '' === $value ) {
            delete_post_meta( $post_id, $meta_key );
            return;
        }

        update_post_meta( $post_id, $meta_key, $value );
    }

    private function save_checkbox_field( int $post_id, string $meta_key, string $post_key ): void {
        $value = isset( $_POST[ $post_key ] ) ? '1' : '0';
        update_post_meta( $post_id, $meta_key, $value );
    }

    private function save_number_field( int $post_id, string $meta_key, string $post_key ): void {
        if ( ! isset( $_POST[ $post_key ] ) ) {
            return;
        }

        $value = absint( wp_unslash( $_POST[ $post_key ] ) );
        update_post_meta( $post_id, $meta_key, (string) $value );
    }

    private function save_date_field( int $post_id, string $meta_key, string $post_key ): void {
        if ( ! isset( $_POST[ $post_key ] ) ) {
            return;
        }

        $value = sanitize_text_field( wp_unslash( $_POST[ $post_key ] ) );

        if ( '' === $value ) {
            delete_post_meta( $post_id, $meta_key );
            return;
        }

        if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $value ) ) {
            return;
        }

        update_post_meta( $post_id, $meta_key, $value );
    }

    private function save_initials_field( int $post_id, string $meta_key, string $post_key ): void {
        if ( ! isset( $_POST[ $post_key ] ) ) {
            return;
        }

        $value = sanitize_text_field( wp_unslash( $_POST[ $post_key ] ) );
        $value = strtoupper( substr( $value, 0, 3 ) );

        if ( '' === $value ) {
            delete_post_meta( $post_id, $meta_key );
            return;
        }

        update_post_meta( $post_id, $meta_key, $value );
    }
}
