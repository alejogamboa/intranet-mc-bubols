<?php
/**
 * Gestión de empresa asociada a cada usuario — MC Intranet Core.
 *
 * Almacena la empresa en user_meta bajo la clave mc_user_company.
 * Expone MC_Intranet_User_Company::get_user_company() para que
 * class-access-control.php pueda verificar acceso por empresa.
 *
 * @package MC_Intranet_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MC_Intranet_User_Company {

	const META_KEY = 'mc_user_company';

	const COMPANIES = [
		'anstra'  => 'Projection Anstra',
		'essenza' => 'Essenza Foods',
		'budefry' => 'Budefry SAS',
	];

	public function __construct() {
		add_action( 'show_user_profile',        [ $this, 'render_company_field' ] );
		add_action( 'edit_user_profile',        [ $this, 'render_company_field' ] );
		add_action( 'personal_options_update',  [ $this, 'save_company_field' ] );
		add_action( 'edit_user_profile_update', [ $this, 'save_company_field' ] );
		add_filter( 'manage_users_columns',        [ $this, 'add_users_column' ] );
		add_filter( 'manage_users_custom_column',  [ $this, 'render_users_column' ], 10, 3 );
	}

	public function render_company_field( WP_User $user ): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$current = (string) get_user_meta( $user->ID, self::META_KEY, true );
		?>
		<h3><?php esc_html_e( 'Empresa', 'mc-intranet-core' ); ?></h3>
		<table class="form-table">
			<tr>
				<th><label for="mc_user_company"><?php esc_html_e( 'Empresa asociada', 'mc-intranet-core' ); ?></label></th>
				<td>
					<?php wp_nonce_field( 'mc_user_company_save_' . $user->ID, 'mc_user_company_nonce' ); ?>
					<select id="mc_user_company" name="mc_user_company">
						<option value=""><?php esc_html_e( '— Sin empresa asignada —', 'mc-intranet-core' ); ?></option>
						<?php foreach ( self::COMPANIES as $slug => $label ) : ?>
							<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $current, $slug ); ?>>
								<?php echo esc_html( $label ); ?>
							</option>
						<?php endforeach; ?>
					</select>
					<p class="description">
						<?php esc_html_e( 'Determina a qué portal de empresa tiene acceso este usuario.', 'mc-intranet-core' ); ?>
					</p>
				</td>
			</tr>
		</table>
		<?php
	}

	public function save_company_field( int $user_id ): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( ! isset( $_POST['mc_user_company_nonce'] ) ) {
			return;
		}

		$nonce = sanitize_text_field( wp_unslash( $_POST['mc_user_company_nonce'] ) );
		if ( ! wp_verify_nonce( $nonce, 'mc_user_company_save_' . $user_id ) ) {
			return;
		}

		$company = isset( $_POST['mc_user_company'] )
			? sanitize_key( wp_unslash( $_POST['mc_user_company'] ) )
			: '';

		if ( '' === $company || ! array_key_exists( $company, self::COMPANIES ) ) {
			delete_user_meta( $user_id, self::META_KEY );
		} else {
			update_user_meta( $user_id, self::META_KEY, $company );
		}
	}

	public function add_users_column( array $columns ): array {
		$columns['mc_company'] = __( 'Empresa', 'mc-intranet-core' );
		return $columns;
	}

	public function render_users_column( string $output, string $column_name, int $user_id ): string {
		if ( 'mc_company' !== $column_name ) {
			return $output;
		}

		$company = (string) get_user_meta( $user_id, self::META_KEY, true );

		if ( ! $company || ! isset( self::COMPANIES[ $company ] ) ) {
			return '—';
		}

		return esc_html( self::COMPANIES[ $company ] );
	}

	/**
	 * Retorna el slug de empresa del usuario, o cadena vacía si no tiene.
	 */
	public static function get_user_company( int $user_id ): string {
		$company = (string) get_user_meta( $user_id, self::META_KEY, true );
		return array_key_exists( $company, self::COMPANIES ) ? $company : '';
	}
}
