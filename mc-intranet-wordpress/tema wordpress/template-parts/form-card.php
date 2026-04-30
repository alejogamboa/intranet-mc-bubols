<?php
/**
 * Template part for displaying form cards.
 *
 * This template can be used to create reusable form card structures throughout the site.
 */

// Check if the form data is set
if ( isset( $args['form_data'] ) ) {
    $form_data = $args['form_data'];
} else {
    $form_data = array(
        'title' => 'Default Title',
        'description' => 'Default description for the form card.',
        'button_text' => 'Submit',
        'form_link' => '#',
    );
}
?>

<div class="form-card">
    <h3 class="form-card__title"><?php echo esc_html( $form_data['title'] ); ?></h3>
    <p class="form-card__description"><?php echo esc_html( $form_data['description'] ); ?></p>
    <a href="<?php echo esc_url( $form_data['form_link'] ); ?>" class="form-card__button">
        <?php echo esc_html( $form_data['button_text'] ); ?>
    </a>
</div>