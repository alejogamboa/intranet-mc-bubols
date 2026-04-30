<?php
/**
 * Company Badge Template Part
 *
 * This template part displays the company badge in the navigation or header,
 * indicating the active company context based on the current page.
 */

// Get the company context from the body class
$company_context = get_post_meta(get_the_ID(), 'company_context', true);

// Define company colors and names
$companies = array(
    'anstra' => array(
        'name' => 'Projection Anstra',
        'color' => '#1A2E52',
    ),
    'essenza' => array(
        'name' => 'Essenza Foods',
        'color' => '#1B6B45',
    ),
    'budefry' => array(
        'name' => 'Budefry SAS',
        'color' => '#2D3748',
    ),
    'interactua' => array(
        'name' => 'Interactúa',
        'color' => '#4338CA',
    ),
);

// Check if the company context is set and valid
if (array_key_exists($company_context, $companies)) {
    $company = $companies[$company_context];
    ?>
    <div class="company-badge" style="background-color: <?php echo esc_attr($company['color']); ?>;">
        <span><?php echo esc_html($company['name']); ?></span>
    </div>
    <?php
}
?>