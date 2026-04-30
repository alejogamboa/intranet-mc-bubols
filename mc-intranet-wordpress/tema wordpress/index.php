<?php
/**
 * Main fallback template file.
 *
 * @package MCIntranetWordPress
 */

get_header();
?>

<main class="site-main container" role="main">
  <?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>
      <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <header class="entry-header">
          <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
        </header>

        <div class="entry-content">
          <?php the_content(); ?>
        </div>
      </article>
    <?php endwhile; ?>
  <?php else : ?>
    <article class="no-results not-found">
      <header class="entry-header">
        <h1 class="entry-title">Contenido no disponible</h1>
      </header>
      <div class="entry-content">
        <p>No se encontraron publicaciones para mostrar.</p>
      </div>
    </article>
  <?php endif; ?>
</main>

<?php
get_footer();
