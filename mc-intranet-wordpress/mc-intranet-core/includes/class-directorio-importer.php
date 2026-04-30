<?php
/**
 * Importador CSV para mc_directorio_contactos — MC Intranet Core.
 *
 * Registra una página de administración bajo el CPT mc_directorio_contactos
 * que permite cargar un archivo CSV con la estructura:
 *   Area;Cargo;Nombre;Celular;email;empresa
 *
 * @package MC_Intranet_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MC_Intranet_Directorio_Importer {

    /** Nonce action para el formulario de importación */
    const NONCE_ACTION = 'mc_directorio_import_csv';
    const NONCE_NAME   = 'mc_directorio_import_nonce';

    /** Empresas permitidas */
    const ALLOWED_COMPANIES = [ 'mc', 'anstra', 'essenza', 'budefry', 'interactua' ];

    public function __construct() {
        add_action( 'admin_menu', [ $this, 'register_admin_page' ] );
        add_action( 'admin_post_mc_directorio_import', [ $this, 'handle_import' ] );
    }

    // ─── Menú de administración ───────────────────────────────────────────────

    public function register_admin_page(): void {
        add_submenu_page(
            'edit.php?post_type=mc_directorio',
            __( 'Importar CSV', 'mc-intranet-core' ),
            __( 'Importar CSV', 'mc-intranet-core' ),
            'manage_options',
            'mc-directorio-import',
            [ $this, 'render_admin_page' ]
        );
    }

    // ─── Renderizado de la página de importación ──────────────────────────────

    public function render_admin_page(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'No tienes permisos para acceder a esta página.', 'mc-intranet-core' ) );
        }
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Importar Directorio de Contactos — CSV', 'mc-intranet-core' ); ?></h1>

            <?php
            // Mostrar aviso de resultado si viene de una redirección.
            if ( ! empty( $_GET['import_type'] ) && ! empty( $_GET['import_msg'] ) ) {
                $notice_type = 'success' === sanitize_key( $_GET['import_type'] ) ? 'success' : 'error';
                $notice_msg  = sanitize_text_field( urldecode( wp_unslash( $_GET['import_msg'] ) ) );
                printf(
                    '<div class="notice notice-%s is-dismissible"><p>%s</p></div>',
                    esc_attr( $notice_type ),
                    esc_html( $notice_msg )
                );
            }
            ?>

            <div class="notice notice-info">
                <p><?php esc_html_e( 'El archivo CSV debe usar punto y coma (;) como separador y tener la siguiente cabecera:', 'mc-intranet-core' ); ?></p>
                <code>Area;Cargo;Nombre;Celular;email;empresa</code>
                <p><?php esc_html_e( 'Empresas válidas: mc, anstra, essenza, budefry, interactua', 'mc-intranet-core' ); ?></p>
                <p><?php esc_html_e( 'Los contactos existentes con el mismo Nombre + empresa serán actualizados; los nuevos, creados.', 'mc-intranet-core' ); ?></p>
            </div>

            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data">
                <input type="hidden" name="action" value="mc_directorio_import" />
                <?php wp_nonce_field( self::NONCE_ACTION, self::NONCE_NAME ); ?>

                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row">
                            <label for="mc_csv_file"><?php esc_html_e( 'Archivo CSV', 'mc-intranet-core' ); ?></label>
                        </th>
                        <td>
                            <input type="file" id="mc_csv_file" name="mc_csv_file" accept=".csv,text/csv" required />
                            <p class="description"><?php esc_html_e( 'Codificación recomendada: UTF-8', 'mc-intranet-core' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Modo', 'mc-intranet-core' ); ?></th>
                        <td>
                            <fieldset>
                                <label>
                                    <input type="radio" name="mc_import_mode" value="upsert" checked />
                                    <?php esc_html_e( 'Crear nuevos + actualizar existentes (recomendado)', 'mc-intranet-core' ); ?>
                                </label><br />
                                <label>
                                    <input type="radio" name="mc_import_mode" value="skip" />
                                    <?php esc_html_e( 'Solo crear nuevos (omitir existentes)', 'mc-intranet-core' ); ?>
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                </table>

                <?php submit_button( __( 'Importar contactos', 'mc-intranet-core' ) ); ?>
            </form>
        </div>
        <?php
    }

    // ─── Procesamiento de la importación ─────────────────────────────────────

    public function handle_import(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'No tienes permisos para realizar esta acción.', 'mc-intranet-core' ) );
        }

        if ( ! isset( $_POST[ self::NONCE_NAME ] ) ||
             ! wp_verify_nonce(
                 sanitize_text_field( wp_unslash( $_POST[ self::NONCE_NAME ] ) ),
                 self::NONCE_ACTION
             )
        ) {
            wp_die( esc_html__( 'Verificación de seguridad fallida.', 'mc-intranet-core' ) );
        }

        // Validar archivo subido.
        if ( empty( $_FILES['mc_csv_file']['tmp_name'] ) ) {
            $this->redirect_with_notice( 'error', __( 'No se recibió ningún archivo.', 'mc-intranet-core' ) );
            return;
        }

        $file     = $_FILES['mc_csv_file'];
        $tmp_path = $file['tmp_name'];

        // Validar tipo MIME básico (no confiar en el nombre, revisar contenido).
        $finfo    = new finfo( FILEINFO_MIME_TYPE );
        $mime     = $finfo->file( $tmp_path );
        $allowed_mimes = [ 'text/plain', 'text/csv', 'application/csv', 'application/octet-stream' ];
        if ( ! in_array( $mime, $allowed_mimes, true ) ) {
            $this->redirect_with_notice( 'error', __( 'El archivo debe ser un CSV de texto plano.', 'mc-intranet-core' ) );
            return;
        }

        $mode = isset( $_POST['mc_import_mode'] ) && 'skip' === $_POST['mc_import_mode'] ? 'skip' : 'upsert';

        $result = $this->process_csv( $tmp_path, $mode );

        if ( is_wp_error( $result ) ) {
            $this->redirect_with_notice( 'error', $result->get_error_message() );
            return;
        }

        $message = sprintf(
            /* translators: 1: created, 2: updated, 3: skipped, 4: errors */
            __( 'Importación completada. Creados: %1$d | Actualizados: %2$d | Omitidos: %3$d | Errores: %4$d', 'mc-intranet-core' ),
            $result['created'],
            $result['updated'],
            $result['skipped'],
            $result['errors']
        );

        $this->redirect_with_notice( 'success', $message );
    }

    // ─── Procesamiento del CSV ────────────────────────────────────────────────

    /**
     * Lee el archivo CSV e inserta/actualiza los posts.
     *
     * @param  string $file_path Ruta absoluta al archivo temporal.
     * @param  string $mode      'upsert' | 'skip'
     * @return array|WP_Error    Array con contadores o WP_Error.
     */
    private function process_csv( string $file_path, string $mode ) {
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
        $handle = fopen( $file_path, 'r' );
        if ( false === $handle ) {
            return new WP_Error( 'csv_open', __( 'No se pudo abrir el archivo CSV.', 'mc-intranet-core' ) );
        }

        $counts = [
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors'  => 0,
        ];

        $line_num = 0;

        while ( ( $row = fgetcsv( $handle, 1000, ';' ) ) !== false ) {
            $line_num++;

            // Saltar cabecera
            if ( 1 === $line_num ) {
                continue;
            }

            // Ignorar líneas en blanco
            if ( empty( array_filter( $row ) ) ) {
                continue;
            }

            if ( count( $row ) < 6 ) {
                $counts['errors']++;
                continue;
            }

            $area    = sanitize_text_field( trim( $row[0] ) );
            $cargo   = sanitize_text_field( trim( $row[1] ) );
            $nombre  = sanitize_text_field( trim( $row[2] ) );
            $celular = sanitize_text_field( trim( $row[3] ) );
            $email   = sanitize_email( trim( $row[4] ) );
            $empresa = sanitize_key( trim( $row[5] ) );

            if ( empty( $nombre ) || empty( $empresa ) ) {
                $counts['errors']++;
                continue;
            }

            if ( ! in_array( $empresa, self::ALLOWED_COMPANIES, true ) ) {
                $counts['errors']++;
                continue;
            }

            // Buscar post existente por nombre + empresa
            $existing_id = $this->find_existing_contact( $nombre, $empresa );

            if ( $existing_id && 'skip' === $mode ) {
                $counts['skipped']++;
                continue;
            }

            if ( $existing_id ) {
                // Actualizar
                wp_update_post( [
                    'ID'         => $existing_id,
                    'post_title' => $nombre,
                    'post_status' => 'publish',
                ] );

                $this->update_contact_meta( $existing_id, $area, $cargo, $nombre, $celular, $email, $empresa );
                $counts['updated']++;
            } else {
                // Crear nuevo
                $post_id = wp_insert_post( [
                    'post_type'   => 'mc_directorio',
                    'post_title'  => $nombre,
                    'post_status' => 'publish',
                    'post_name'   => sanitize_title( $nombre . '-' . $empresa ),
                ] );

                if ( is_wp_error( $post_id ) ) {
                    $counts['errors']++;
                    continue;
                }

                $this->update_contact_meta( $post_id, $area, $cargo, $nombre, $celular, $email, $empresa );
                $counts['created']++;
            }
        }

        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
        fclose( $handle );

        return $counts;
    }

    /**
     * Busca un contacto existente por nombre y empresa.
     *
     * @return int|null  Post ID o null si no existe.
     */
    private function find_existing_contact( string $nombre, string $empresa ): ?int {
        $query = new WP_Query( [
            'post_type'      => 'mc_directorio',
            'post_status'    => [ 'publish', 'draft' ],
            'posts_per_page' => 1,
            'meta_query'     => [
                'relation' => 'AND',
                [
                    'key'     => 'nombre',
                    'value'   => $nombre,
                    'compare' => '=',
                ],
                [
                    'key'     => 'company_context',
                    'value'   => $empresa,
                    'compare' => '=',
                ],
            ],
            'fields'         => 'ids',
        ] );

        if ( $query->have_posts() ) {
            return (int) $query->posts[0];
        }

        return null;
    }

    /**
     * Actualiza los post metas de un contacto.
     */
    private function update_contact_meta(
        int    $post_id,
        string $area,
        string $cargo,
        string $nombre,
        string $celular,
        string $email,
        string $empresa
    ): void {
        update_post_meta( $post_id, 'area',            $area );
        update_post_meta( $post_id, 'cargo',           $cargo );
        update_post_meta( $post_id, 'nombre',          $nombre );
        update_post_meta( $post_id, 'celular',         $celular );
        update_post_meta( $post_id, 'email',           $email );
        update_post_meta( $post_id, 'company_context', $empresa );
    }

    // ─── Redirección con aviso ────────────────────────────────────────────────

    private function redirect_with_notice( string $type, string $message ): void {
        $redirect = add_query_arg(
            [
                'post_type'    => 'mc_directorio',
                'page'         => 'mc-directorio-import',
                'import_type'  => rawurlencode( $type ),
                'import_msg'   => rawurlencode( $message ),
            ],
            admin_url( 'edit.php' )
        );

        wp_safe_redirect( $redirect );
        exit;
    }
}
