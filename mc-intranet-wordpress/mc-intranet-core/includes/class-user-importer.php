<?php
/**
 * Importador de usuarios desde CSV — MC Intranet Core.
 *
 * Agrega una página en Usuarios > Importar CSV.
 * Rol asignado: subscriber. Contraseña por defecto configurable.
 * Campo empresa vinculado a MC_Intranet_User_Company.
 *
 * @package MC_Intranet_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MC_Intranet_User_Importer {

	const DEFAULT_PASSWORD = 'Intranet2026!';
	const MENU_SLUG        = 'mc-importar-usuarios';

	/** Columnas requeridas en el CSV (en minúsculas). */
	const REQUIRED_COLS = [ 'email', 'nombre', 'apellido', 'empresa' ];

	public function __construct() {
		add_action( 'admin_menu',                                       [ $this, 'register_menu' ] );
		add_action( 'admin_post_mc_download_users_csv_sample',          [ $this, 'download_sample_csv' ] );
	}

	// ─── Menú ────────────────────────────────────────────────────────────────────

	public function register_menu(): void {
		add_users_page(
			__( 'Importar usuarios CSV', 'mc-intranet-core' ),
			__( 'Importar CSV', 'mc-intranet-core' ),
			'manage_options',
			self::MENU_SLUG,
			[ $this, 'render_page' ]
		);
	}

	// ─── Página ──────────────────────────────────────────────────────────────────

	public function render_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Acceso denegado.', 'mc-intranet-core' ) );
		}

		$results    = null;
		$has_error  = false;

		if (
			'POST' === $_SERVER['REQUEST_METHOD']
			&& isset( $_POST['mc_import_users_nonce'] )
			&& wp_verify_nonce(
				sanitize_text_field( wp_unslash( $_POST['mc_import_users_nonce'] ) ),
				'mc_import_users'
			)
		) {
			$results   = $this->process_import();
			$has_error = isset( $results['error'] );
		}

		$sample_url  = wp_nonce_url(
			admin_url( 'admin-post.php?action=mc_download_users_csv_sample' ),
			'mc_download_sample'
		);
		$company_str = implode( ', ', array_keys( MC_Intranet_User_Company::COMPANIES ) );

		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Importar usuarios desde CSV', 'mc-intranet-core' ); ?></h1>

			<p><?php esc_html_e( 'Sube un archivo CSV (UTF-8, separador coma) para crear usuarios en lote. Cada usuario creado recibe el rol de suscriptor y la contraseña por defecto que se muestra abajo.', 'mc-intranet-core' ); ?></p>

			<h2 style="font-size:14px;"><?php esc_html_e( 'Columnas del CSV', 'mc-intranet-core' ); ?></h2>
			<table class="widefat" style="max-width:660px;margin-bottom:16px;">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Columna', 'mc-intranet-core' ); ?></th>
						<th><?php esc_html_e( 'Req.', 'mc-intranet-core' ); ?></th>
						<th><?php esc_html_e( 'Descripción', 'mc-intranet-core' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr><td><code>email</code></td>   <td>✓</td><td><?php esc_html_e( 'Correo electrónico (usado como identificador único)', 'mc-intranet-core' ); ?></td></tr>
					<tr><td><code>nombre</code></td>  <td>✓</td><td><?php esc_html_e( 'Nombre(s)', 'mc-intranet-core' ); ?></td></tr>
					<tr><td><code>apellido</code></td><td>✓</td><td><?php esc_html_e( 'Apellido(s)', 'mc-intranet-core' ); ?></td></tr>
					<tr>
						<td><code>empresa</code></td><td>✓</td>
						<td>
							<?php esc_html_e( 'Slug de empresa:', 'mc-intranet-core' ); ?>
							<code><?php echo esc_html( $company_str ); ?></code>
						</td>
					</tr>
					<tr><td><code>usuario</code></td> <td></td><td><?php esc_html_e( 'Nombre de login (se genera desde el email si se omite)', 'mc-intranet-core' ); ?></td></tr>
				</tbody>
			</table>

			<p>
				<strong><?php esc_html_e( 'Contraseña por defecto:', 'mc-intranet-core' ); ?></strong>
				<code><?php echo esc_html( self::DEFAULT_PASSWORD ); ?></code>
			</p>

			<p>
				<a href="<?php echo esc_url( $sample_url ); ?>" class="button">
					&#11015; <?php esc_html_e( 'Descargar CSV de ejemplo', 'mc-intranet-core' ); ?>
				</a>
			</p>

			<hr style="margin:24px 0;" />

			<form method="post" enctype="multipart/form-data" style="max-width:560px;">
				<?php wp_nonce_field( 'mc_import_users', 'mc_import_users_nonce' ); ?>
				<table class="form-table" role="presentation">
					<tr>
						<th><label for="mc_csv_file"><?php esc_html_e( 'Archivo CSV', 'mc-intranet-core' ); ?></label></th>
						<td>
							<input type="file" id="mc_csv_file" name="mc_csv_file" accept=".csv,text/csv" required />
							<p class="description"><?php esc_html_e( 'Formato: UTF-8, separador coma, primera fila como cabecera.', 'mc-intranet-core' ); ?></p>
						</td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Notificación', 'mc-intranet-core' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="mc_send_notification" value="1" />
								<?php esc_html_e( 'Enviar correo de bienvenida a cada usuario creado', 'mc-intranet-core' ); ?>
							</label>
						</td>
					</tr>
				</table>
				<p>
					<button type="submit" class="button button-primary">
						<?php esc_html_e( 'Importar usuarios', 'mc-intranet-core' ); ?>
					</button>
				</p>
			</form>

			<?php if ( null !== $results ) : ?>
				<hr style="margin:24px 0;" />
				<h2><?php esc_html_e( 'Resultado de la importación', 'mc-intranet-core' ); ?></h2>

				<?php if ( $has_error ) : ?>
					<div class="notice notice-error inline"><p><?php echo esc_html( $results['error'] ); ?></p></div>

				<?php else : ?>
					<p>
						<strong><?php esc_html_e( 'Creados:', 'mc-intranet-core' ); ?></strong> <?php echo (int) $results['created']; ?> &nbsp;&nbsp;
						<strong><?php esc_html_e( 'Omitidos (ya existen):', 'mc-intranet-core' ); ?></strong> <?php echo (int) $results['skipped']; ?> &nbsp;&nbsp;
						<strong><?php esc_html_e( 'Con error:', 'mc-intranet-core' ); ?></strong> <?php echo count( $results['errors'] ); ?>
					</p>

					<?php if ( ! empty( $results['rows'] ) ) : ?>
						<table class="widefat striped" style="max-width:860px;">
							<thead>
								<tr>
									<th><?php esc_html_e( 'Fila', 'mc-intranet-core' ); ?></th>
									<th><?php esc_html_e( 'Email', 'mc-intranet-core' ); ?></th>
									<th><?php esc_html_e( 'Login', 'mc-intranet-core' ); ?></th>
									<th><?php esc_html_e( 'Nombre', 'mc-intranet-core' ); ?></th>
									<th><?php esc_html_e( 'Empresa', 'mc-intranet-core' ); ?></th>
									<th><?php esc_html_e( 'Estado', 'mc-intranet-core' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $results['rows'] as $i => $row ) : ?>
									<tr>
										<td><?php echo (int) ( $i + 2 ); ?></td>
										<td><?php echo esc_html( $row['email'] ); ?></td>
										<td><?php echo esc_html( $row['login'] ); ?></td>
										<td><?php echo esc_html( $row['display_name'] ); ?></td>
										<td>
											<?php
											$slug  = $row['empresa'];
											$label = MC_Intranet_User_Company::COMPANIES[ $slug ] ?? $slug;
											echo esc_html( $label );
											?>
										</td>
										<td>
											<?php if ( 'created' === $row['status'] ) : ?>
												<span style="color:#00a32a;">&#10003; <?php esc_html_e( 'Creado', 'mc-intranet-core' ); ?></span>
											<?php elseif ( 'skipped' === $row['status'] ) : ?>
												<span style="color:#996800;">&#9888; <?php esc_html_e( 'Ya existe', 'mc-intranet-core' ); ?></span>
											<?php else : ?>
												<span style="color:#cc1818;">&#10007; <?php echo esc_html( $row['message'] ?? __( 'Error', 'mc-intranet-core' ) ); ?></span>
											<?php endif; ?>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					<?php endif; ?>
				<?php endif; ?>
			<?php endif; ?>
		</div>
		<?php
	}

	// ─── Procesamiento ───────────────────────────────────────────────────────────

	private function process_import(): array {
		if (
			empty( $_FILES['mc_csv_file']['tmp_name'] )
			|| UPLOAD_ERR_OK !== (int) $_FILES['mc_csv_file']['error']
		) {
			return [ 'error' => __( 'No se recibió ningún archivo o hubo un error al subirlo.', 'mc-intranet-core' ) ];
		}

		$tmp_path = $_FILES['mc_csv_file']['tmp_name'];

		if ( ! is_uploaded_file( $tmp_path ) ) {
			return [ 'error' => __( 'Archivo no válido.', 'mc-intranet-core' ) ];
		}

		// Quitar BOM UTF-8 si lo hay.
		$raw = file_get_contents( $tmp_path );
		if ( false === $raw ) {
			return [ 'error' => __( 'No se pudo leer el archivo.', 'mc-intranet-core' ) ];
		}
		if ( str_starts_with( $raw, "\xEF\xBB\xBF" ) ) {
			$raw = substr( $raw, 3 );
		}
		file_put_contents( $tmp_path, $raw );

		$handle = fopen( $tmp_path, 'r' );
		if ( ! $handle ) {
			return [ 'error' => __( 'No se pudo abrir el archivo.', 'mc-intranet-core' ) ];
		}

		$header = fgetcsv( $handle );
		if ( ! $header ) {
			fclose( $handle );
			return [ 'error' => __( 'El archivo está vacío o no es un CSV válido.', 'mc-intranet-core' ) ];
		}

		$header  = array_map( fn( $h ) => strtolower( trim( $h ) ), $header );
		$missing = array_diff( self::REQUIRED_COLS, $header );

		if ( $missing ) {
			fclose( $handle );
			return [
				'error' => sprintf(
					__( 'Faltan columnas requeridas: %s', 'mc-intranet-core' ),
					implode( ', ', $missing )
				),
			];
		}

		$col    = array_flip( $header );
		$notify = ! empty( $_POST['mc_send_notification'] );

		$results = [
			'created' => 0,
			'skipped' => 0,
			'errors'  => [],
			'rows'    => [],
		];

		while ( ( $line = fgetcsv( $handle ) ) !== false ) {
			if ( array_filter( $line, fn( $v ) => '' !== trim( $v ) ) === [] ) {
				continue;
			}

			$email    = isset( $col['email'] )    ? sanitize_email( trim( $line[ $col['email'] ] ) )                    : '';
			$nombre   = isset( $col['nombre'] )   ? sanitize_text_field( trim( $line[ $col['nombre'] ] ) )              : '';
			$apellido = isset( $col['apellido'] ) ? sanitize_text_field( trim( $line[ $col['apellido'] ] ) )            : '';
			$empresa  = isset( $col['empresa'] )  ? sanitize_key( trim( $line[ $col['empresa'] ] ) )                   : '';
			$login    = isset( $col['usuario'] )  ? sanitize_user( trim( $line[ $col['usuario'] ] ), true )             : '';

			// Validar email.
			if ( ! is_email( $email ) ) {
				$results['errors'][] = $email ?: '(sin email)';
				$results['rows'][]   = [
					'email'        => $email,
					'login'        => $login,
					'display_name' => trim( "$nombre $apellido" ),
					'empresa'      => $empresa,
					'status'       => 'error',
					'message'      => __( 'Email inválido', 'mc-intranet-core' ),
				];
				continue;
			}

			// Generar login desde email si no viene.
			if ( '' === $login ) {
				$login = sanitize_user( strstr( $email, '@', true ), true );
			}
			$login        = $this->unique_login( $login );
			$display_name = trim( "$nombre $apellido" );

			// Saltar si el email ya existe.
			if ( email_exists( $email ) ) {
				$results['skipped']++;
				$results['rows'][] = [
					'email'        => $email,
					'login'        => $login,
					'display_name' => $display_name,
					'empresa'      => $empresa,
					'status'       => 'skipped',
				];
				continue;
			}

			$user_id = wp_insert_user( [
				'user_login'   => $login,
				'user_email'   => $email,
				'user_pass'    => self::DEFAULT_PASSWORD,
				'first_name'   => $nombre,
				'last_name'    => $apellido,
				'display_name' => $display_name,
				'role'         => 'subscriber',
			] );

			if ( is_wp_error( $user_id ) ) {
				$results['errors'][] = $email;
				$results['rows'][]   = [
					'email'        => $email,
					'login'        => $login,
					'display_name' => $display_name,
					'empresa'      => $empresa,
					'status'       => 'error',
					'message'      => $user_id->get_error_message(),
				];
				continue;
			}

			// Asignar empresa solo si el slug es válido.
			if ( array_key_exists( $empresa, MC_Intranet_User_Company::COMPANIES ) ) {
				update_user_meta( $user_id, MC_Intranet_User_Company::META_KEY, $empresa );
			}

			if ( $notify ) {
				wp_new_user_notification( $user_id, null, 'user' );
			}

			$results['created']++;
			$results['rows'][] = [
				'email'        => $email,
				'login'        => $login,
				'display_name' => $display_name,
				'empresa'      => $empresa,
				'status'       => 'created',
			];
		}

		fclose( $handle );

		return $results;
	}

	// ─── Helpers ─────────────────────────────────────────────────────────────────

	/** Garantiza que el login sea único añadiendo un sufijo numérico si hace falta. */
	private function unique_login( string $login ): string {
		$base    = $login;
		$counter = 1;

		while ( username_exists( $login ) ) {
			$login = $base . $counter;
			$counter++;
		}

		return $login;
	}

	// ─── Descarga CSV de ejemplo ─────────────────────────────────────────────────

	public function download_sample_csv(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Acceso denegado.', 'mc-intranet-core' ) );
		}

		check_admin_referer( 'mc_download_sample' );

		$rows = [
			[ 'email', 'nombre', 'apellido', 'usuario', 'empresa' ],
			[ 'maria.garcia@anstra.com',    'María',    'García',    'maria.garcia',    'anstra'  ],
			[ 'carlos.lopez@essenza.com',   'Carlos',   'López',     'carlos.lopez',    'essenza' ],
			[ 'ana.martinez@budefry.com',   'Ana',      'Martínez',  'ana.martinez',    'budefry' ],
			[ 'luis.rodriguez@anstra.com',  'Luis',     'Rodríguez', 'luis.rodriguez',  'anstra'  ],
			[ 'sofia.herrera@essenza.com',  'Sofía',    'Herrera',   'sofia.herrera',   'essenza' ],
		];

		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="usuarios-ejemplo.csv"' );
		header( 'Cache-Control: no-cache, no-store, must-revalidate' );
		header( 'Pragma: no-cache' );

		$output = fopen( 'php://output', 'w' );

		// BOM UTF-8 para compatibilidad con Excel.
		fwrite( $output, "\xEF\xBB\xBF" );

		foreach ( $rows as $row ) {
			fputcsv( $output, $row );
		}

		fclose( $output );
		exit;
	}
}
