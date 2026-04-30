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

$type_labels = [
    'academico'   => 'Académico',
    'profesional' => 'Profesional',
    'cultural'    => 'Cultural',
];
$type_label = $type_labels[ $rec_data['type'] ] ?? esc_html( $rec_data['type'] );
?>
<div class="recognition-card">
    <div class="recognition-card__avatar" aria-hidden="true">
        <?php echo esc_html( $rec_data['initials'] ?: mb_substr( $rec_data['name'], 0, 2 ) ); ?>
    </div>
    <div class="recognition-card__body">
        <p class="recognition-card__name"><?php echo esc_html( $rec_data['name'] ); ?></p>
        <?php if ( $rec_data['role'] ) : ?>
        <p class="recognition-card__role"><?php echo esc_html( $rec_data['role'] ); ?></p>
        <?php endif; ?>
        <?php if ( $rec_data['company'] ) : ?>
        <span class="tag"><?php echo esc_html( $rec_data['company'] ); ?></span>
        <?php endif; ?>
        <span class="tag"><?php echo esc_html( $type_label ); ?></span>
        <?php if ( $rec_data['excerpt'] ) : ?>
        <p class="recognition-card__excerpt"><?php echo esc_html( $rec_data['excerpt'] ); ?></p>
        <?php endif; ?>
    </div>
</div>
