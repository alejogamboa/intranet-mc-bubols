<?php
/**
 * Template para posts individuales — MC Intranet.
 *
 * Detecta si el post usa Elementor Builder:
 *   - SI: wrapper mínimo (Elementor controla el layout completo).
 *   - NO: diseño editorial completo con hero, meta, tipografía y posts relacionados.
 *
 * @package MC_Intranet
 */

get_header();

while ( have_posts() ) :
    the_post();

    $post_id      = get_the_ID();
    $thumbnail_id = get_post_thumbnail_id( $post_id );
    $has_image    = has_post_thumbnail( $post_id );
    $categories   = get_the_category( $post_id );
    $primary_cat  = ! empty( $categories ) ? $categories[0] : null;
    $tags         = get_the_tags( $post_id );

    // ── Detectar Elementor ──────────────────────────────────────────────────
    $is_elementor = 'builder' === (string) get_post_meta( $post_id, '_elementor_edit_mode', true );

    if ( $is_elementor ) :
        // Wrapper mínimo: Elementor controla el layout
        ?>
        <main id="main" class="site-main site-main--elementor" role="main">
            <article id="post-<?php the_ID(); ?>" <?php post_class( 'sp-elementor-post' ); ?>>
                <?php the_content(); ?>
            </article>
        </main>
        <?php
    else :
        // ── Diseño editorial completo ───────────────────────────────────────
        $company_context = mc_get_company_context();
        $prev_post       = get_previous_post();
        $next_post       = get_next_post();

        // Posts relacionados por categoría
        $related_args = [
            'post_type'      => 'post',
            'posts_per_page' => 3,
            'post_status'    => 'publish',
            'post__not_in'   => [ $post_id ],
            'orderby'        => 'date',
            'order'          => 'DESC',
        ];
        if ( ! empty( $categories ) ) {
            $related_args['category__in'] = wp_list_pluck( $categories, 'term_id' );
        }
        $related_query = new WP_Query( $related_args );
        ?>

        <main id="main" class="site-main" role="main">

            <!-- ── HERO ──────────────────────────────────────────────────── -->
            <div class="sp-hero<?php echo $has_image ? ' sp-hero--has-image' : ' sp-hero--gradient'; ?>" role="banner">
                <?php if ( $has_image ) : ?>
                    <div class="sp-hero__bg" aria-hidden="true">
                        <?php the_post_thumbnail( 'full', [ 'class' => 'sp-hero__img' ] ); ?>
                    </div>
                <?php endif; ?>
                <div class="sp-hero__overlay" aria-hidden="true"></div>

                <div class="container sp-hero__inner">

                    <!-- Breadcrumb -->
                    <nav class="sp-breadcrumb" aria-label="<?php esc_attr_e( 'Ruta de navegación', 'mc-intranet' ); ?>">
                        <ol class="sp-breadcrumb__list">
                            <li>
                                <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V21h14V9.5"/></svg>
                                    <?php esc_html_e( 'Inicio', 'mc-intranet' ); ?>
                                </a>
                            </li>
                            <?php if ( $primary_cat ) : ?>
                                <li aria-hidden="true" class="sp-breadcrumb__sep">›</li>
                                <li>
                                    <a href="<?php echo esc_url( get_category_link( $primary_cat->term_id ) ); ?>">
                                        <?php echo esc_html( $primary_cat->name ); ?>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <li aria-hidden="true" class="sp-breadcrumb__sep">›</li>
                            <li aria-current="page"><?php the_title(); ?></li>
                        </ol>
                    </nav>

                    <!-- Categoría pill -->
                    <?php if ( $primary_cat ) : ?>
                        <a href="<?php echo esc_url( get_category_link( $primary_cat->term_id ) ); ?>" class="sp-hero__cat-badge">
                            <?php echo esc_html( $primary_cat->name ); ?>
                        </a>
                    <?php endif; ?>

                    <!-- Título -->
                    <h1 class="sp-hero__title"><?php the_title(); ?></h1>

                    <!-- Meta -->
                    <div class="sp-hero__meta">
                        <span class="sp-hero__meta-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                                <?php echo esc_html( get_the_date( 'd M Y' ) ); ?>
                            </time>
                        </span>
                        <?php if ( ! empty( $categories ) ) : ?>
                            <span class="sp-hero__meta-sep" aria-hidden="true">·</span>
                            <span class="sp-hero__meta-item">
                                <?php
                                $cat_links = [];
                                foreach ( $categories as $cat ) {
                                    $cat_links[] = sprintf(
                                        '<a href="%s" class="sp-hero__cat-link">%s</a>',
                                        esc_url( get_category_link( $cat->term_id ) ),
                                        esc_html( $cat->name )
                                    );
                                }
                                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                echo implode( '<span aria-hidden="true">, </span>', $cat_links );
                                ?>
                            </span>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
            <!-- /HERO -->

            <!-- ── LAYOUT PRINCIPAL ───────────────────────────────────────── -->
            <div class="container sp-layout">

                <!-- Barra de progreso de lectura -->
                <div class="sp-progress" aria-hidden="true">
                    <div class="sp-progress__bar" id="sp-progress-bar"></div>
                </div>

                <article id="post-<?php the_ID(); ?>" <?php post_class( 'sp-article' ); ?>>

                    <!-- Contenido principal -->
                    <div class="sp-article__content entry-content">
                        <?php the_content(); ?>
                    </div>

                    <!-- Footer del artículo: tags + nav entre posts -->
                    <footer class="sp-article__footer">

                        <?php if ( $tags ) : ?>
                            <div class="sp-tags" aria-label="<?php esc_attr_e( 'Etiquetas', 'mc-intranet' ); ?>">
                                <span class="sp-tags__label">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 2H2v10l9.29 9.29a1 1 0 0 0 1.41 0l7.29-7.29a1 1 0 0 0 0-1.41z"/><circle cx="7" cy="7" r="1"/></svg>
                                    <?php esc_html_e( 'Etiquetas:', 'mc-intranet' ); ?>
                                </span>
                                <?php foreach ( $tags as $tag ) : ?>
                                    <a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>" class="sp-tag">
                                        <?php echo esc_html( $tag->name ); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Navegación entre posts -->
                        <?php if ( $prev_post || $next_post ) : ?>
                            <nav class="sp-post-nav" aria-label="<?php esc_attr_e( 'Navegación entre publicaciones', 'mc-intranet' ); ?>">
                                <div class="sp-post-nav__item sp-post-nav__item--prev">
                                    <?php if ( $prev_post ) : ?>
                                        <a href="<?php echo esc_url( get_permalink( $prev_post->ID ) ); ?>" class="sp-post-nav__link">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
                                            <span>
                                                <span class="sp-post-nav__label"><?php esc_html_e( 'Anterior', 'mc-intranet' ); ?></span>
                                                <span class="sp-post-nav__title"><?php echo esc_html( get_the_title( $prev_post->ID ) ); ?></span>
                                            </span>
                                        </a>
                                    <?php endif; ?>
                                </div>
                                <div class="sp-post-nav__item sp-post-nav__item--next">
                                    <?php if ( $next_post ) : ?>
                                        <a href="<?php echo esc_url( get_permalink( $next_post->ID ) ); ?>" class="sp-post-nav__link">
                                            <span>
                                                <span class="sp-post-nav__label"><?php esc_html_e( 'Siguiente', 'mc-intranet' ); ?></span>
                                                <span class="sp-post-nav__title"><?php echo esc_html( get_the_title( $next_post->ID ) ); ?></span>
                                            </span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </nav>
                        <?php endif; ?>

                        <!-- Volver -->
                        <div class="sp-back">
                            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="sp-back__btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
                                <?php esc_html_e( 'Volver al inicio', 'mc-intranet' ); ?>
                            </a>
                        </div>

                    </footer>
                </article>

                <!-- ── POSTS RELACIONADOS ─────────────────────────────────── -->
                <?php if ( $related_query->have_posts() ) : ?>
                    <aside class="sp-related" aria-label="<?php esc_attr_e( 'Publicaciones relacionadas', 'mc-intranet' ); ?>">
                        <h2 class="sp-related__heading">
                            <?php esc_html_e( 'También te puede interesar', 'mc-intranet' ); ?>
                        </h2>
                        <div class="sp-related__grid">
                            <?php while ( $related_query->have_posts() ) : $related_query->the_post(); ?>
                                <?php
                                $rel_cats = get_the_category();
                                $rel_cat  = ! empty( $rel_cats ) ? $rel_cats[0] : null;
                                ?>
                                <article class="sp-related__card">
                                    <a href="<?php the_permalink(); ?>" class="sp-related__card-inner">
                                        <div class="sp-related__card-media">
                                            <?php if ( has_post_thumbnail() ) : ?>
                                                <?php the_post_thumbnail( 'medium_large', [ 'class' => 'sp-related__card-img', 'loading' => 'lazy' ] ); ?>
                                            <?php else : ?>
                                                <div class="sp-related__card-placeholder" aria-hidden="true">
                                                    <?php
                                                    $rel_initials = '';
                                                    $rel_words    = preg_split( '/\s+/u', trim( get_the_title() ) );
                                                    if ( is_array( $rel_words ) ) {
                                                        foreach ( $rel_words as $w ) {
                                                            if ( '' === $w ) { continue; }
                                                            $rel_initials .= mb_strtoupper( mb_substr( $w, 0, 1 ) );
                                                            if ( mb_strlen( $rel_initials ) >= 2 ) { break; }
                                                        }
                                                    }
                                                    echo esc_html( $rel_initials ?: '--' );
                                                    ?>
                                                </div>
                                            <?php endif; ?>
                                            <div class="sp-related__card-overlay" aria-hidden="true"></div>
                                        </div>
                                        <div class="sp-related__card-body">
                                            <?php if ( $rel_cat ) : ?>
                                                <span class="sp-related__card-cat"><?php echo esc_html( $rel_cat->name ); ?></span>
                                            <?php endif; ?>
                                            <h3 class="sp-related__card-title"><?php the_title(); ?></h3>
                                            <time class="sp-related__card-date" datetime="<?php the_date( 'c' ); ?>">
                                                <?php the_date( 'd M Y' ); ?>
                                            </time>
                                        </div>
                                    </a>
                                </article>
                            <?php endwhile; wp_reset_postdata(); ?>
                        </div>
                    </aside>
                <?php endif; ?>

            </div>
            <!-- /LAYOUT PRINCIPAL -->

        </main>

        <script>
        (function () {
            var bar     = document.getElementById('sp-progress-bar');
            var article = document.querySelector('.sp-article__content');
            if (!bar || !article) return;

            function updateProgress() {
                var rect   = article.getBoundingClientRect();
                var total  = article.offsetHeight - window.innerHeight;
                var scroll = -rect.top;
                var pct    = total > 0 ? Math.min(Math.max(scroll / total * 100, 0), 100) : 0;
                bar.style.width = pct + '%';
            }

            window.addEventListener('scroll', updateProgress, { passive: true });
            updateProgress();
        })();
        </script>

        <?php
    endif; // end !$is_elementor
endwhile;

get_footer();
