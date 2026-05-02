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

// Formatear fecha para UI: chip con dia y mes.
$date_obj      = $event_data['date'] ? date_create( $event_data['date'] ) : false;
$date_display  = $date_obj ? date_format( $date_obj, 'd/m/Y' ) : $event_data['date'];
$date_day      = $date_obj ? date_format( $date_obj, 'd' ) : $date_display;
$date_month    = $date_obj ? strtoupper( wp_date( 'M', $date_obj->getTimestamp() ) ) : '';
$mode_location = trim( (string) $event_data['location'] );
$gallery       = is_array( $event_data['gallery'] ?? null ) ? $event_data['gallery'] : [];

$title_words       = preg_split( '/\s+/', trim( wp_strip_all_tags( (string) $event_data['title'] ) ) );
$image_monogram    = '';
$image_words_count = is_array( $title_words ) ? count( $title_words ) : 0;

if ( is_array( $title_words ) ) {
    foreach ( $title_words as $word ) {
        if ( '' === $word ) {
            continue;
        }

        $image_monogram .= strtoupper( substr( $word, 0, 1 ) );

        if ( strlen( $image_monogram ) >= 2 ) {
            break;
        }
    }
}

$image_monogram = $image_monogram ? $image_monogram : 'EV';
$media_class    = 'event-item__media' . ( $gallery ? '' : ' event-item__media--placeholder' );

$item_class = 'event-item';
if ( $event_data['featured'] ) {
    $item_class .= ' event-item--featured';
}

$gallery_images = array_slice( $gallery, 0, 2 );
$extra_images = max( count( $gallery ) - 2, 0 );
$lightbox_items = [];

foreach ( $gallery as $image ) {
    $lightbox_items[] = [
        'url' => (string) ( $image['url'] ?? '' ),
        'alt' => trim( (string) ( $image['alt'] ?? '' ) ) ?: (string) $event_data['title'],
    ];
}

$lightbox_gallery = esc_attr( wp_json_encode( $lightbox_items ) ?: '[]' );
?>
<article class="<?php echo esc_attr( $item_class ); ?>">
    <div class="event-item__main">
        <div class="event-item__date" aria-label="<?php echo esc_attr( $date_display ); ?>">
            <span class="event-item__day"><?php echo esc_html( $date_day ); ?></span>
            <?php if ( $date_month ) : ?>
            <span class="event-item__month"><?php echo esc_html( $date_month ); ?></span>
            <?php endif; ?>
        </div>
        <div class="event-item__content">
            <?php if ( $mode_location ) : ?>
            <p class="event-item__eyebrow">
                <span class="event-item__eyebrow-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" role="presentation" focusable="false">
                        <path d="M12 22s7-6.2 7-12a7 7 0 1 0-14 0c0 5.8 7 12 7 12Zm0-9a3 3 0 1 1 0-6 3 3 0 0 1 0 6Z" fill="currentColor"/>
                    </svg>
                </span>
                <span><?php echo esc_html( $mode_location ); ?></span>
            </p>
            <?php endif; ?>
            <?php if ( $event_data['featured'] ) : ?>
            <p class="event-item__meta">
                <span class="event-item__featured-badge">Destacado</span>
            </p>
            <?php endif; ?>
            <h3 class="event-item__title"><?php echo esc_html( $event_data['title'] ); ?></h3>
            <?php if ( $event_data['content'] ) : ?>
            <div class="event-item__desc"><?php echo wp_kses_post( $event_data['content'] ); ?></div>
            <?php endif; ?>
            <?php if ( $event_data['mode'] ) : ?>
            <div class="event-item__details" aria-label="Modalidad del evento">
                <span class="event-item__detail-pill"><?php echo esc_html( $mode_label ); ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <figure class="<?php echo esc_attr( $media_class ); ?>">
        <?php if ( $gallery_images ) : ?>
        <div class="event-item__gallery-grid">
            <?php foreach ( $gallery_images as $index => $image ) : ?>
            <div class="event-item__gallery-item<?php echo $extra_images > 0 && 1 === $index ? ' event-item__gallery-item--more' : ''; ?>">
                <?php if ( $extra_images > 0 && 1 === $index ) : ?>
                <button
                    type="button"
                    class="event-item__gallery-more-trigger"
                    data-mc-lightbox-gallery="<?php echo $lightbox_gallery; ?>"
                    data-mc-lightbox-start-index="2"
                    aria-label="<?php echo esc_attr( sprintf( 'Ver galería completa, %d imágenes adicionales', $extra_images ) ); ?>"
                >
                <?php endif; ?>
                <img class="event-item__image" src="<?php echo esc_url( (string) $image['url'] ); ?>" alt="<?php echo esc_attr( trim( (string) ( $image['alt'] ?? '' ) ) ?: (string) $event_data['title'] ); ?>" loading="lazy" decoding="async" />
                <?php if ( $extra_images > 0 && 1 === $index ) : ?>
                <span class="event-item__gallery-count">+<?php echo esc_html( (string) $extra_images ); ?></span>
                </button>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else : ?>
        <div class="event-item__placeholder" aria-hidden="true">
            <span class="event-item__placeholder-mark"><?php echo esc_html( $image_monogram ); ?></span>
            <span class="event-item__placeholder-label">Agenda MC</span>
            <?php if ( $image_words_count > 1 ) : ?>
            <span class="event-item__placeholder-line"></span>
            <?php endif; ?>
        </div>
        <figcaption class="sr-only">Sin galería para <?php echo esc_html( $event_data['title'] ); ?></figcaption>
        <?php endif; ?>
    </figure>
</article>
