<?php
/**
 * Template: recognition-card.php
 * Variables disponibles: $rec_data (array)
 *
 * @package MC_Intranet_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$has_image    = ! empty( $rec_data['image_url'] );
$fallback     = mb_strtoupper( mb_substr( $rec_data['name'], 0, 2 ) );
$image_alt    = trim( (string) ( $rec_data['image_alt'] ?? '' ) );
$image_alt    = $image_alt ?: $rec_data['name'];
$avatar_class = 'recognition-card__avatar' . ( $has_image ? ' recognition-card__avatar--photo' : '' );
?>
<div class="recognition-card<?php echo $has_image ? ' recognition-card--with-image' : ''; ?>">
    <?php if ( $has_image ) : ?>
    <figure class="recognition-card__media">
        <img src="<?php echo esc_url( $rec_data['image_url'] ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>" loading="lazy" decoding="async" />
    </figure>
    <?php else : ?>
    <div class="<?php echo esc_attr( $avatar_class ); ?>" aria-hidden="true">
        <?php echo esc_html( $fallback ); ?>
    </div>
    <?php endif; ?>
    <div class="recognition-card__body">
        <p class="recognition-card__badge" aria-label="Reconocimiento destacado">🏆 ✨</p>
        <p class="recognition-card__name"><?php echo esc_html( $rec_data['name'] ); ?></p>
        <?php if ( $rec_data['desc'] ) : ?>
        <div class="recognition-card__desc"><?php echo wp_kses_post( $rec_data['desc'] ); ?></div>
        <?php endif; ?>
    </div>
</div>
