<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?> data-company="<?php echo mc_get_data_company_attr(); ?>">
<?php wp_body_open(); ?>

<nav class="global-nav" role="navigation" aria-label="<?php esc_attr_e( 'Navegación principal', 'mc-intranet' ); ?>">
    <div class="container global-nav__inner">

        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="global-nav__logo" aria-label="<?php esc_attr_e( 'MC Intranet — Inicio', 'mc-intranet' ); ?>">
            <div class="global-nav__logo-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg>
            </div>
            <div class="global-nav__logo-text">
                <span class="global-nav__logo-name">MC Intranet</span>
                <span class="global-nav__logo-sub">Portal Corporativo</span>
            </div>
        </a>

        <?php
        $nav_items = [
            [
                'url'   => home_url( '/' ),
                'label' => 'Inicio',
                'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V21h14V9.5"/></svg>',
            ],
            [
                'url'   => home_url( '/anstra/' ),
                'label' => 'Projection Anstra',
                'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="18" rx="1"/><rect x="14" y="8" width="7" height="13" rx="1"/></svg>',
            ],
            [
                'url'   => home_url( '/essenza/' ),
                'label' => 'Essenza Foods',
                'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 2v4"/><path d="M18 2v4"/><rect x="3" y="6" width="18" height="15" rx="2"/><path d="M3 10h18"/></svg>',
            ],
            [
                'url'   => home_url( '/budefry/' ),
                'label' => 'Budefry',
                'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 21h18"/><path d="M5 21V8l7-4v17"/><path d="M12 9h7v12"/></svg>',
            ],
            [
                'url'   => home_url( '/interactua/' ),
                'label' => 'Interactúa',
                'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v4"/><path d="M12 17v4"/><path d="M3 12h4"/><path d="M17 12h4"/><path d="m5.6 5.6 2.8 2.8"/><path d="m15.6 15.6 2.8 2.8"/><path d="m18.4 5.6-2.8 2.8"/><path d="m8.4 15.6-2.8 2.8"/></svg>',
            ],
        ];

        echo '<ul class="global-nav__links" role="list" id="nav-links">';
        foreach ( $nav_items as $item ) {
            $is_current = untrailingslashit( home_url( add_query_arg( [], $GLOBALS['wp']->request ) ) ) === untrailingslashit( $item['url'] );
            if ( home_url( '/' ) === $item['url'] && is_front_page() ) {
                $is_current = true;
            }

            printf(
                '<li><a href="%s" class="global-nav__link"%s>%s%s</a></li>',
                esc_url( $item['url'] ),
                $is_current ? ' aria-current="page"' : '',
                $item['icon'],
                esc_html( $item['label'] )
            );
        }
        echo '</ul>';
        ?>

        <?php
        $company_context = mc_get_company_context();
        $company_labels  = [
            'anstra'     => 'Projection Anstra',
            'essenza'    => 'Essenza Foods',
            'budefry'    => 'Budefry SAS',
            'interactua' => 'Interactúa',
            'default'    => 'Multicompañía',
        ];
        $company_label = $company_labels[ $company_context ] ?? 'Multicompañía';
        $company_logo  = mc_get_company_logo_img( $company_context, 'company-logo company-logo--badge', '', false );
        $badge_class   = 'global-nav__company-badge' . ( $company_logo ? ' global-nav__company-badge--with-logo' : '' );
        ?>
        <div class="<?php echo esc_attr( $badge_class ); ?>">
            <?php if ( $company_logo ) : ?>
                <?php echo $company_logo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            <?php endif; ?>
            <?php echo esc_html( $company_label ); ?>
        </div>

        <button
            class="global-nav__toggle"
            aria-label="<?php esc_attr_e( 'Menú', 'mc-intranet' ); ?>"
            aria-expanded="false"
            aria-controls="nav-links"
            id="nav-toggle"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
        </button>

    </div>
</nav>
