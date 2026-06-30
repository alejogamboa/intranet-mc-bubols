<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?> data-company="<?php echo mc_get_data_company_attr(); ?>">
  <?php wp_body_open(); ?>

  <?php if ( function_exists( 'hfe_header_enabled' ) && hfe_header_enabled() ) : ?>
    <?php do_action( 'hfe_header' ); ?>
  <?php else : ?>

  <?php
  $nav_bg_color    = '';
  $nav_company_ctx = mc_get_company_context();
  if ( 'default' !== $nav_company_ctx && class_exists( 'MC_Intranet_Branding_Settings' ) ) {
    $nav_company_settings = MC_Intranet_Branding_Settings::get_company_settings( $nav_company_ctx );
    $nav_bg_color         = sanitize_hex_color( (string) ( $nav_company_settings['hero_bg_color'] ?? '' ) );
  }
  $nav_inline_style = $nav_bg_color ? ' style="background-color:' . esc_attr( $nav_bg_color ) . ';"' : '';
  ?>
  <nav class="global-nav" role="navigation" aria-label="<?php esc_attr_e('Navegación principal', 'mc-intranet'); ?>"<?php echo $nav_inline_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
    <div class="container global-nav__inner">

      <a href="<?php echo esc_url(home_url('/')); ?>" class="global-nav__logo" aria-label="<?php esc_attr_e('MC Intranet — Inicio', 'mc-intranet'); ?>">
        <div class="global-nav__logo-icon">
          <img src="/wp-content/themes/mc_intranet/assets/img/logos/mc-blanco.png" alt="">
        </div>
        <div class="global-nav__logo-text">
          <span class="global-nav__logo-name">Intranet</span>
          <span class="global-nav__logo-sub">Portal Corporativo</span>
        </div>
      </a>

      <?php
      $anstra_label  = mc_get_company_display_name( 'anstra', 'Projection Anstra' );
      $essenza_label = mc_get_company_display_name( 'essenza', 'Essenza Foods' );
      $budefry_label = mc_get_company_display_name( 'budefry', 'Budefry SAS' );

      wp_nav_menu( [
        'theme_location' => 'primary',
        'container'      => false,
        'menu_id'        => 'nav-links',
        'menu_class'     => 'global-nav__links',
        'items_wrap'     => '<ul id="%1$s" class="%2$s" role="list">%3$s</ul>',
        'fallback_cb'    => 'mc_intranet_nav_fallback',
      ] );
      ?>

      <?php
      $company_context = mc_get_company_context();
      $company_labels  = [
        'anstra'     => $anstra_label,
        'essenza'    => $essenza_label,
        'budefry'    => $budefry_label,
        'interactua' => 'Interactúa',
        'default'    => 'Multicompañía',
      ];
      $company_label = $company_labels[$company_context] ?? 'Multicompañía';
      $company_logo  = mc_get_company_logo_img($company_context, 'company-logo company-logo--badge', '', false);
      $badge_class   = 'global-nav__company-badge' . ($company_logo ? ' global-nav__company-badge--with-logo' : '');
      ?>
      <div class="<?php echo esc_attr($badge_class); ?>">
        <?php if ($company_logo) : ?>
          <?php echo $company_logo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
          ?>
        <?php endif; ?>
        <?php echo esc_html($company_label); ?>
      </div>

      <button
        class="global-nav__toggle"
        aria-label="<?php esc_attr_e('Menú', 'mc-intranet'); ?>"
        aria-expanded="false"
        aria-controls="nav-links"
        id="nav-toggle">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <line x1="4" x2="20" y1="12" y2="12" />
          <line x1="4" x2="20" y1="6" y2="6" />
          <line x1="4" x2="20" y1="18" y2="18" />
        </svg>
      </button>

    </div>
  </nav>
  <?php endif; ?>