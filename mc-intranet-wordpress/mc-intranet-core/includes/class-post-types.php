<?php

/**
 * Registro de Custom Post Types — MC Intranet Core.
 *
 * CPTs:
 *  - mc_formulario    → Formularios y documentos de gestión
 *  - mc_evento        → Eventos del portal Interactúa
 *  - mc_reconocimiento → Reconocimientos corporativos
 *  - mc_sede          → Sedes para el footer global
 *
 * @package MC_Intranet_Core
 */

if (! defined('ABSPATH')) {
  exit;
}

class MC_Intranet_Post_Types
{

  public function __construct()
  {
    add_action('init', [$this, 'register']);
  }

  public function register(): void
  {
    $this->register_formulario();
    $this->register_evento();
    $this->register_reconocimiento();
    $this->register_sede();
    $this->register_directorio_contactos();
  }

  // ─── mc_formulario ───────────────────────────────────────────────────────

  private function register_formulario(): void
  {
    register_post_type('mc_formulario', [
      'labels'              => [
        'name'               => __('Formularios', 'mc-intranet-core'),
        'singular_name'      => __('Formulario', 'mc-intranet-core'),
        'add_new_item'       => __('Agregar Formulario', 'mc-intranet-core'),
        'edit_item'          => __('Editar Formulario', 'mc-intranet-core'),
        'search_items'       => __('Buscar Formularios', 'mc-intranet-core'),
        'not_found'          => __('No se encontraron formularios.', 'mc-intranet-core'),
        'not_found_in_trash' => __('No hay formularios en la papelera.', 'mc-intranet-core'),
      ],
      'public'              => false,
      'show_ui'             => true,
      'show_in_menu'        => true,
      'show_in_rest'        => true,
      'menu_icon'           => 'dashicons-feedback',
      'menu_position'       => 25,
      'supports'            => ['title', 'editor', 'thumbnail', 'page-attributes'],
      'capability_type'     => 'post',
      'map_meta_cap'        => true,
      'rewrite'             => false,
      'has_archive'         => false,
    ]);
  }

  // ─── mc_evento ───────────────────────────────────────────────────────────

  private function register_evento(): void
  {
    register_post_type('mc_evento', [
      'labels'          => [
        'name'          => __('Eventos', 'mc-intranet-core'),
        'singular_name' => __('Evento', 'mc-intranet-core'),
        'add_new_item'  => __('Agregar Evento', 'mc-intranet-core'),
        'edit_item'     => __('Editar Evento', 'mc-intranet-core'),
      ],
      'public'          => false,
      'show_ui'         => true,
      'show_in_menu'    => true,
      'show_in_rest'    => true,
      'menu_icon'       => 'dashicons-calendar-alt',
      'menu_position'   => 26,
      'supports'        => ['title', 'editor', 'thumbnail'],
      'rewrite'         => false,
      'has_archive'     => false,
    ]);
  }

  // ─── mc_reconocimiento ───────────────────────────────────────────────────

  private function register_reconocimiento(): void
  {
    register_post_type('mc_reconocimiento', [
      'labels'          => [
        'name'          => __('Reconocimientos', 'mc-intranet-core'),
        'singular_name' => __('Reconocimiento', 'mc-intranet-core'),
        'add_new_item'  => __('Agregar Reconocimiento', 'mc-intranet-core'),
        'edit_item'     => __('Editar Reconocimiento', 'mc-intranet-core'),
      ],
      'public'          => false,
      'show_ui'         => true,
      'show_in_menu'    => true,
      'show_in_rest'    => true,
      'menu_icon'       => 'dashicons-awards',
      'menu_position'   => 27,
      'supports'        => ['title', 'editor', 'thumbnail'],
      'rewrite'         => false,
      'has_archive'     => false,
    ]);
  }

  // ─── mc_directorio ──────────────────────────────────────────────────────

  private function register_directorio_contactos(): void
  {
    register_post_type( 'mc_directorio', [
      'labels'          => [
        'name'               => __( 'Directorio Contactos', 'mc-intranet-core' ),
        'singular_name'      => __( 'Contacto', 'mc-intranet-core' ),
        'add_new_item'       => __( 'Agregar Contacto', 'mc-intranet-core' ),
        'edit_item'          => __( 'Editar Contacto', 'mc-intranet-core' ),
        'search_items'       => __( 'Buscar Contactos', 'mc-intranet-core' ),
        'not_found'          => __( 'No se encontraron contactos.', 'mc-intranet-core' ),
        'not_found_in_trash' => __( 'No hay contactos en la papelera.', 'mc-intranet-core' ),
      ],
      'public'          => false,
      'show_ui'         => true,
      'show_in_menu'    => true,
      'show_in_rest'    => true,
      'menu_icon'       => 'dashicons-id-alt',
      'menu_position'   => 29,
      'supports'        => [ 'title' ],
      'rewrite'         => false,
      'has_archive'     => false,
      'capability_type' => 'post',
      'map_meta_cap'    => true,
    ] );
  }

  // ─── mc_sede ─────────────────────────────────────────────────────────────

  private function register_sede(): void
  {
    register_post_type('mc_sede', [
      'labels'          => [
        'name'          => __('Sedes', 'mc-intranet-core'),
        'singular_name' => __('Sede', 'mc-intranet-core'),
        'add_new_item'  => __('Agregar Sede', 'mc-intranet-core'),
        'edit_item'     => __('Editar Sede', 'mc-intranet-core'),
      ],
      'public'          => false,
      'show_ui'         => true,
      'show_in_menu'    => true,
      'show_in_rest'    => true,
      'menu_icon'       => 'dashicons-location',
      'menu_position'   => 28,
      'supports'        => ['title', 'page-attributes'],
      'rewrite'         => false,
      'has_archive'     => false,
    ]);
  }
}
