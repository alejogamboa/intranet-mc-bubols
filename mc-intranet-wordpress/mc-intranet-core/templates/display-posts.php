<?php
/**
 * Template: display-posts.php
 * Variables disponibles:
 *   $posts    (array)  — Posts preparados por shortcode_display_posts()
 *   $subtitle (string) — Subtítulo de sección (ya sanitizado)
 *   $empresa  (string) — Slug de empresa (reservado)
 *
 * @package MC_Intranet_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$instance_id = 'dp-' . wp_rand( 1000, 999999 );

$dp_color_bg   = [ '#EEF2FF', '#FEF3C7', '#DCFCE7', '#FCE7F3', '#E0F2FE', '#FFF7ED', '#F3E8FF', '#ECFDF5' ];
$dp_color_text = [ '#3730A3', '#92400E', '#166534', '#9D174D', '#075985', '#C2410C', '#6B21A8', '#065F46' ];
$dp_accent_hex = [ '#818CF8', '#FCD34D', '#4ADE80', '#F9A8D4', '#38BDF8', '#FB923C', '#C084FC', '#34D399' ];
?>
<div id="<?php echo esc_attr( $instance_id ); ?>" class="dp-section company--<?php echo esc_attr( $empresa ?: 'interactua' ); ?>">

	<?php if ( $subtitle ) : ?>
	<div class="dp-section__header">
		<h2 class="dp-section__subtitle"><?php echo esc_html( $subtitle ); ?></h2>
	</div>
	<?php endif; ?>

	<div class="dp-toolbar">
		<div class="dp-search-wrap">
			<label class="screen-reader-text" for="<?php echo esc_attr( $instance_id ); ?>-search">
				<?php
				/* translators: %s: section subtitle */
				printf( esc_html__( 'Buscar en %s', 'mc-intranet-core' ), esc_html( $subtitle ) );
				?>
			</label>
			<div class="dp-search-inner">
				<svg class="dp-search-icon" xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
				<input
					id="<?php echo esc_attr( $instance_id ); ?>-search"
					class="dp-search"
					type="search"
					placeholder="<?php esc_attr_e( 'Buscar por título, categoría o contenido…', 'mc-intranet-core' ); ?>"
					autocomplete="off"
				/>
			</div>
		</div>
	</div>

	<div class="dp-grid" role="list" aria-label="<?php echo esc_attr( $subtitle ); ?>">
		<?php foreach ( $posts as $i => $post ) : ?>
			<?php
			$has_image  = ! empty( $post['image_url'] );
			$img_alt    = trim( (string) $post['image_alt'] ) ?: esc_attr( (string) $post['title'] );
			$parts      = preg_split( '/\s+/u', trim( (string) $post['title'] ) );
			$initials   = '';
			if ( is_array( $parts ) ) {
				foreach ( $parts as $part ) {
					if ( '' === $part ) {
						continue;
					}
					$initials .= mb_strtoupper( mb_substr( $part, 0, 1 ) );
					if ( mb_strlen( $initials ) >= 2 ) {
						break;
					}
				}
			}

			$card_accent = 'var(--color-primary, #4338CA)';
			if ( ! empty( $post['categories'] ) ) {
				$first_idx   = abs( crc32( (string) $post['categories'][0]['slug'] ) ) % 8;
				$card_accent = $dp_accent_hex[ $first_idx ];
			}
			?>
			<article
				class="dp-card"
				role="listitem"
				data-search="<?php echo esc_attr( (string) $post['search_text'] ); ?>"
				style="--dp-delay:<?php echo esc_attr( (string) min( $i, 11 ) ); ?>;--dp-accent:<?php echo esc_attr( $card_accent ); ?>"
			>
				<a
					class="dp-card__inner"
					href="<?php echo esc_url( (string) $post['permalink'] ); ?>"
					aria-label="<?php echo esc_attr( sprintf( __( 'Leer: %s', 'mc-intranet-core' ), (string) $post['title'] ) ); ?>"
				>
					<div class="dp-card__media-wrap">
						<?php if ( $has_image ) : ?>
							<figure class="dp-card__media">
								<img
									class="dp-card__img"
									src="<?php echo esc_url( (string) $post['image_url'] ); ?>"
									alt="<?php echo esc_attr( $img_alt ); ?>"
									loading="lazy"
									decoding="async"
								/>
							</figure>
						<?php else : ?>
							<div class="dp-card__placeholder" aria-hidden="true">
								<span class="dp-card__initials"><?php echo esc_html( $initials ?: '--' ); ?></span>
							</div>
						<?php endif; ?>
						<div class="dp-card__media-overlay" aria-hidden="true"></div>
					</div>

					<div class="dp-card__body">
						<?php if ( ! empty( $post['categories'] ) ) : ?>
							<div class="dp-pills" aria-label="<?php esc_attr_e( 'Categorías', 'mc-intranet-core' ); ?>">
								<?php foreach ( $post['categories'] as $cat ) : ?>
									<?php
									$color_idx  = abs( crc32( (string) $cat['slug'] ) ) % 8;
									$pill_style = 'background:' . $dp_color_bg[ $color_idx ] . ';color:' . $dp_color_text[ $color_idx ] . ';';
									?>
									<span class="dp-pill" style="<?php echo esc_attr( $pill_style ); ?>">
										<?php echo esc_html( (string) $cat['name'] ); ?>
									</span>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>

						<h3 class="dp-card__title"><?php echo esc_html( (string) $post['title'] ); ?></h3>

						<?php if ( $post['excerpt'] ) : ?>
							<p class="dp-card__excerpt"><?php echo esc_html( (string) $post['excerpt'] ); ?></p>
						<?php endif; ?>
					</div>

					<div class="dp-card__footer">
						<time class="dp-card__date" datetime="<?php echo esc_attr( (string) $post['date_iso'] ); ?>">
							<svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
							<?php echo esc_html( (string) $post['date'] ); ?>
						</time>
						<span class="dp-card__cta" aria-hidden="true">
							<?php esc_html_e( 'Leer', 'mc-intranet-core' ); ?>
							<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
						</span>
					</div>

					<div class="dp-card__accent-bar" aria-hidden="true"></div>
				</a>
			</article>
		<?php endforeach; ?>
	</div>

	<div class="dp-empty-state" hidden>
		<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/><path d="M8 11h6"/></svg>
		<p><?php esc_html_e( 'No hay resultados para esta búsqueda.', 'mc-intranet-core' ); ?></p>
	</div>

</div>

<style>
@keyframes dpFadeUp {
	from { opacity: 0; transform: translateY(14px); }
	to   { opacity: 1; transform: none; }
}

.dp-section {
	display: flex;
	flex-direction: column;
	gap: 1.5rem;
	font-family: var(--font-body, 'Inter', system-ui, sans-serif);
}

/* ── Header ───────────────────────────────────────────────── */
.dp-section__header { margin: 0; }

.dp-section__subtitle {
	font-size: 1.1875rem;
	font-weight: 700;
	color: #111827;
	margin: 0;
	display: flex;
	align-items: center;
	gap: 0.625rem;
}

.dp-section__subtitle::before {
	content: '';
	display: inline-block;
	width: 4px;
	height: 1.1em;
	background: var(--color-primary, #4338CA);
	border-radius: 2px;
	flex-shrink: 0;
}

/* ── Toolbar / Search ─────────────────────────────────────── */
.dp-toolbar {
	display: flex;
	justify-content: flex-end;
}

.dp-search-wrap {
	width: min(420px, 100%);
}

.dp-search-inner {
	position: relative;
}

.dp-search-icon {
	position: absolute;
	left: 0.8125rem;
	top: 50%;
	transform: translateY(-50%);
	color: #9ca3af;
	pointer-events: none;
	transition: color 0.15s ease;
}

.dp-search-inner:focus-within .dp-search-icon {
	color: var(--color-primary, #4338CA);
}

.dp-search {
	width: 100%;
	height: 42px;
	padding: 0 0.875rem 0 2.375rem;
	border: 1px solid #e5e7eb;
	border-radius: 0.625rem;
	font-size: 0.875rem;
	background: #f9fafb;
	color: #111827;
	transition: border-color 0.15s ease, background 0.15s ease, box-shadow 0.15s ease;
}

.dp-search:focus {
	outline: none;
	background: #fff;
	border-color: var(--color-primary, #4338CA);
	box-shadow: 0 0 0 3px color-mix(in srgb, var(--color-primary, #4338CA) 15%, transparent);
}

/* ── Grid ─────────────────────────────────────────────────── */
.dp-grid {
	display: grid;
	grid-template-columns: 1fr;
	gap: 1.125rem;
	list-style: none;
	margin: 0;
	padding: 0;
}

@media (min-width: 640px) {
	.dp-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (min-width: 1024px) {
	.dp-grid { grid-template-columns: repeat(3, 1fr); }
}

/* ── Card ─────────────────────────────────────────────────── */
.dp-card {
	border-radius: 0.875rem;
	overflow: hidden;
	animation: dpFadeUp 0.4s ease both;
	animation-delay: calc(var(--dp-delay, 0) * 55ms);
}

@media (prefers-reduced-motion: reduce) {
	.dp-card { animation: none; }
}

.dp-card__inner {
	display: flex;
	flex-direction: column;
	height: 100%;
	background: #fff;
	border: 1px solid #e5e7eb;
	border-radius: 0.875rem;
	overflow: hidden;
	text-decoration: none;
	color: inherit;
	transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
	position: relative;
}

.dp-card__inner:hover {
	border-color: #d1d5db;
	box-shadow: 0 6px 24px rgba(0, 0, 0, 0.07), 0 2px 6px rgba(0, 0, 0, 0.04);
	transform: translateY(-3px);
}

/* ── Media ────────────────────────────────────────────────── */
.dp-card__media-wrap {
	position: relative;
	overflow: hidden;
}

.dp-card__media {
	aspect-ratio: 16 / 9;
	overflow: hidden;
	background: #f3f4f6;
	margin: 0;
}

.dp-card__img {
	width: 100%;
	height: 100%;
	object-fit: cover;
	display: block;
	transition: transform 0.45s ease;
}

.dp-card__inner:hover .dp-card__img {
	transform: scale(1.05);
}

.dp-card__placeholder {
	width: 100%;
	aspect-ratio: 16 / 9;
	background: linear-gradient(135deg, var(--color-primary, #4338CA) 0%, color-mix(in srgb, var(--color-primary, #4338CA) 60%, #6366F1) 100%);
	display: flex;
	align-items: center;
	justify-content: center;
}

.dp-card__initials {
	color: rgba(255, 255, 255, 0.5);
	font-size: 2.25rem;
	font-weight: 700;
	letter-spacing: 0.06em;
	transition: color 0.2s ease;
}

.dp-card__inner:hover .dp-card__initials {
	color: rgba(255, 255, 255, 0.7);
}

.dp-card__media-overlay {
	position: absolute;
	inset: 0;
	background: linear-gradient(to top, rgba(0, 0, 0, 0.12), transparent 55%);
	opacity: 0;
	transition: opacity 0.3s ease;
	pointer-events: none;
}

.dp-card__inner:hover .dp-card__media-overlay {
	opacity: 1;
}

/* ── Body ─────────────────────────────────────────────────── */
.dp-card__body {
	flex: 1;
	display: flex;
	flex-direction: column;
	gap: 0.5rem;
	padding: 1rem 1rem 0.75rem;
}

.dp-pills {
	display: inline-flex;
	flex-wrap: wrap;
	gap: 0.25rem;
}

.dp-pill {
	display: inline-flex;
	padding: 0.2rem 0.55rem;
	border-radius: 9999px;
	font-size: 0.6875rem;
	font-weight: 600;
	line-height: 1.4;
	white-space: nowrap;
	letter-spacing: 0.01em;
}

.dp-card__title {
	font-size: 0.9375rem;
	font-weight: 650;
	color: #111827;
	line-height: 1.4;
	margin: 0;
	transition: color 0.15s ease;
}

.dp-card__inner:hover .dp-card__title {
	color: var(--color-primary, #4338CA);
}

.dp-card__excerpt {
	font-size: 0.8125rem;
	color: #6b7280;
	line-height: 1.55;
	margin: 0;
	flex: 1;
	display: -webkit-box;
	-webkit-line-clamp: 3;
	-webkit-box-orient: vertical;
	overflow: hidden;
}

/* ── Footer ───────────────────────────────────────────────── */
.dp-card__footer {
	display: flex;
	align-items: center;
	justify-content: space-between;
	padding: 0.625rem 1rem 0.75rem;
	border-top: 1px solid #f3f4f6;
	margin-top: auto;
}

.dp-card__date {
	display: inline-flex;
	align-items: center;
	gap: 0.3rem;
	font-size: 0.75rem;
	color: #9ca3af;
}

.dp-card__cta {
	display: inline-flex;
	align-items: center;
	gap: 0.25rem;
	font-size: 0.75rem;
	font-weight: 600;
	color: var(--color-primary, #4338CA);
	opacity: 0;
	transform: translateX(-4px);
	transition: opacity 0.2s ease, transform 0.2s ease;
}

.dp-card__inner:hover .dp-card__cta {
	opacity: 1;
	transform: none;
}

/* ── Accent bar ───────────────────────────────────────────── */
.dp-card__accent-bar {
	position: absolute;
	bottom: 0;
	left: 0;
	right: 0;
	height: 3px;
	background: var(--dp-accent, var(--color-primary, #4338CA));
	transform: scaleX(0);
	transform-origin: left center;
	transition: transform 0.3s ease;
	border-radius: 0 0 0.875rem 0.875rem;
}

.dp-card__inner:hover .dp-card__accent-bar {
	transform: scaleX(1);
}

/* ── Empty state ──────────────────────────────────────────── */
.dp-empty-state {
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 0.75rem;
	padding: 3rem 1rem;
	text-align: center;
	color: #9ca3af;
	border: 1.5px dashed #e5e7eb;
	border-radius: 0.875rem;
}

.dp-empty-state[hidden] { display: none; }

.dp-empty-state p {
	margin: 0;
	font-size: 0.9rem;
	color: #6b7280;
}
</style>

<script>
(function () {
	var root = document.getElementById(<?php echo wp_json_encode( $instance_id ); ?>);
	if (!root) {
		return;
	}

	var cards       = root.querySelectorAll('.dp-card');
	var searchInput = root.querySelector('.dp-search');
	var emptyState  = root.querySelector('.dp-empty-state');

	function normalize(text) {
		return (text || '')
			.toString()
			.toLowerCase()
			.normalize('NFD')
			.replace(/[\u0300-\u036f]/g, '');
	}

	function applyFilters() {
		var term    = normalize(searchInput ? searchInput.value : '');
		var visible = 0;

		cards.forEach(function (card) {
			var searchable = normalize(card.getAttribute('data-search') || '');
			var show       = !term || searchable.indexOf(term) !== -1;
			card.hidden    = !show;
			if (show) {
				visible++;
			}
		});

		if (emptyState) {
			emptyState.hidden = visible > 0;
		}
	}

	if (searchInput) {
		searchInput.addEventListener('input', applyFilters);
	}
})();
</script>
