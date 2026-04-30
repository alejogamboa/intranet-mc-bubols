<?php
/**
 * Template: event-item.php
 * Variables disponibles: $event_data (array)
 *
 * @package MC_Intranet_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$mode_labels = [
    'presencial' => 'Presencial',
    'virtual'    => 'Virtual',
    'hibrido'    => 'Híbrido',
];
$mode_label = $mode_labels[ $event_data['mode'] ] ?? esc_html( $event_data['mode'] );

// Formatear fecha: YYYY-MM-DD → día mes año
$date_obj     = $event_data['date'] ? date_create( $event_data['date'] ) : false;
$date_display = $date_obj ? date_format( $date_obj, 'd/m/Y' ) : $event_data['date'];

$item_class = 'event-item';
if ( $event_data['featured'] ) {
    $item_class .= ' event-item--featured';
}
?>
<div class="<?php echo esc_attr( $item_class ); ?>">
    <div class="event-item__dot" aria-hidden="true"></div>
    <div class="event-item__body">
        <p class="event-item__date"><?php echo esc_html( $date_display ); ?></p>
        <h3 class="event-item__title"><?php echo esc_html( $event_data['title'] ); ?></h3>
        <div class="event-item__tags">
            <?php if ( $mode_label ) : ?>
            <span class="tag"><?php echo esc_html( $mode_label ); ?></span>
            <?php endif; ?>
            <?php if ( $event_data['location'] ) : ?>
            <span class="tag">
                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                <?php echo esc_html( $event_data['location'] ); ?>
            </span>
            <?php endif; ?>
        </div>
        <?php if ( $event_data['content'] ) : ?>
        <div class="event-item__content"><?php echo wp_kses_post( $event_data['content'] ); ?></div>
        <?php endif; ?>
    </div>
</div>
