<?php

/**
 * Meta boxes para CPTs de MC Intranet Core.
 *
 * @package MC_Intranet_Core
 */

if (! defined('ABSPATH')) {
  exit;
}

class MC_Intranet_Meta_Boxes
{

  public function __construct()
  {
    add_action('add_meta_boxes', [$this, 'register_meta_boxes']);
    add_action('save_post', [$this, 'save_meta_boxes'], 10, 2);
    add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
  }

  public function enqueue_admin_assets(string $hook_suffix): void
  {
    if (! in_array($hook_suffix, ['post.php', 'post-new.php'], true)) {
      return;
    }

    $screen = get_current_screen();
    if (! $screen || ! in_array($screen->post_type, ['mc_evento', 'mc_sede'], true)) {
      return;
    }

    wp_enqueue_media();
  }

  public function register_meta_boxes(): void
  {
    add_meta_box(
      'mc_formulario_meta',
      __('Datos del Formulario', 'mc-intranet-core'),
      [$this, 'render_formulario_meta_box'],
      'mc_formulario',
      'normal',
      'default'
    );

    add_meta_box(
      'mc_evento_meta',
      __('Datos del Evento', 'mc-intranet-core'),
      [$this, 'render_evento_meta_box'],
      'mc_evento',
      'normal',
      'default'
    );

    add_meta_box(
      'mc_reconocimiento_meta',
      __('Datos del Reconocimiento', 'mc-intranet-core'),
      [$this, 'render_reconocimiento_meta_box'],
      'mc_reconocimiento',
      'normal',
      'default'
    );

    add_meta_box(
      'mc_sede_meta',
      __('Datos de la Sede', 'mc-intranet-core'),
      [$this, 'render_sede_meta_box'],
      'mc_sede',
      'normal',
      'default'
    );

    add_meta_box(
      'mc_directorio_meta',
      __('Datos del Contacto', 'mc-intranet-core'),
      [$this, 'render_directorio_contactos_meta_box'],
      'mc_directorio',
      'normal',
      'default'
    );

    add_meta_box(
      'mc_company_portal_meta',
      __('Datos del Portal de Empresa', 'mc-intranet-core'),
      [$this, 'render_company_portal_meta_box'],
      'mc_company_portal',
      'normal',
      'default'
    );
  }

  public function render_formulario_meta_box(WP_Post $post): void
  {
    wp_nonce_field('mc_intranet_meta_boxes_action', 'mc_intranet_meta_boxes_nonce');

    $company_context = (string) get_post_meta($post->ID, 'company_context', true);
    $area_context    = (string) get_post_meta($post->ID, 'area_context', true);
    $form_type       = (string) get_post_meta($post->ID, 'form_type', true);
    $form_url        = (string) get_post_meta($post->ID, 'form_url', true);
    $cta_label       = (string) get_post_meta($post->ID, 'cta_label', true);
    $open_new_tab    = (string) get_post_meta($post->ID, 'open_new_tab', true);
    $is_featured     = (string) get_post_meta($post->ID, 'is_featured', true);
    $order_weight    = (string) get_post_meta($post->ID, 'order_weight', true);

?>
    <table class="form-table" role="presentation">
      <tr>
        <th><label for="mc_company_context"><?php esc_html_e('Company Context', 'mc-intranet-core'); ?></label></th>
        <td><input type="text" id="mc_company_context" name="mc_company_context" class="regular-text" value="<?php echo esc_attr($company_context); ?>" /></td>
      </tr>
      <tr>
        <th><label for="mc_area_context"><?php esc_html_e('Area Context', 'mc-intranet-core'); ?></label></th>
        <td><input type="text" id="mc_area_context" name="mc_area_context" class="regular-text" value="<?php echo esc_attr($area_context); ?>" /></td>
      </tr>
      <tr>
        <th><label for="mc_form_type"><?php esc_html_e('Form Type', 'mc-intranet-core'); ?></label></th>
        <td><input type="text" id="mc_form_type" name="mc_form_type" class="regular-text" value="<?php echo esc_attr($form_type); ?>" /></td>
      </tr>
      <tr>
        <th><label for="mc_form_url"><?php esc_html_e('Form URL', 'mc-intranet-core'); ?></label></th>
        <td><input type="url" id="mc_form_url" name="mc_form_url" class="regular-text" value="<?php echo esc_attr($form_url); ?>" /></td>
      </tr>
      <tr>
        <th><label for="mc_cta_label"><?php esc_html_e('CTA Label', 'mc-intranet-core'); ?></label></th>
        <td><input type="text" id="mc_cta_label" name="mc_cta_label" class="regular-text" value="<?php echo esc_attr($cta_label); ?>" /></td>
      </tr>
      <tr>
        <th><?php esc_html_e('Open New Tab', 'mc-intranet-core'); ?></th>
        <td><label><input type="checkbox" name="mc_open_new_tab" value="1" <?php checked('1', $open_new_tab); ?> /> <?php esc_html_e('Yes', 'mc-intranet-core'); ?></label></td>
      </tr>
      <tr>
        <th><?php esc_html_e('Featured', 'mc-intranet-core'); ?></th>
        <td><label><input type="checkbox" name="mc_is_featured" value="1" <?php checked('1', $is_featured); ?> /> <?php esc_html_e('Yes', 'mc-intranet-core'); ?></label></td>
      </tr>
      <tr>
        <th><label for="mc_order_weight"><?php esc_html_e('Order Weight', 'mc-intranet-core'); ?></label></th>
        <td><input type="number" id="mc_order_weight" name="mc_order_weight" class="small-text" value="<?php echo esc_attr($order_weight); ?>" min="0" step="1" /></td>
      </tr>
    </table>
  <?php
  }

  public function render_evento_meta_box(WP_Post $post): void
  {
    wp_nonce_field('mc_intranet_meta_boxes_action', 'mc_intranet_meta_boxes_nonce');

    $event_date     = (string) get_post_meta($post->ID, 'event_date', true);
    $event_mode     = (string) get_post_meta($post->ID, 'event_mode', true);
    $event_location = (string) get_post_meta($post->ID, 'event_location', true);
    $event_featured = (string) get_post_meta($post->ID, 'event_featured', true);
    $company_context = (string) get_post_meta($post->ID, 'company_context', true);
    $event_gallery_ids = (string) get_post_meta($post->ID, 'event_gallery_ids', true);
    $gallery_ids = array_values(array_filter(array_map('absint', explode(',', $event_gallery_ids))));

  ?>
    <table class="form-table" role="presentation">
      <tr>
        <th><label for="mc_event_date"><?php esc_html_e('Event Date (YYYY-MM-DD)', 'mc-intranet-core'); ?></label></th>
        <td><input type="date" id="mc_event_date" name="mc_event_date" value="<?php echo esc_attr($event_date); ?>" /></td>
      </tr>
      <tr>
        <th><label for="mc_event_mode"><?php esc_html_e('Event Mode', 'mc-intranet-core'); ?></label></th>
        <td><input type="text" id="mc_event_mode" name="mc_event_mode" class="regular-text" value="<?php echo esc_attr($event_mode); ?>" /></td>
      </tr>
      <tr>
        <th><label for="mc_event_location"><?php esc_html_e('Event Location', 'mc-intranet-core'); ?></label></th>
        <td><input type="text" id="mc_event_location" name="mc_event_location" class="regular-text" value="<?php echo esc_attr($event_location); ?>" /></td>
      </tr>
      <tr>
        <th><label for="mc_event_company_context"><?php esc_html_e('Company Context', 'mc-intranet-core'); ?></label></th>
        <td><input type="text" id="mc_event_company_context" name="mc_event_company_context" class="regular-text" value="<?php echo esc_attr($company_context); ?>" /></td>
      </tr>
      <tr>
        <th><?php esc_html_e('Featured Event', 'mc-intranet-core'); ?></th>
        <td><label><input type="checkbox" name="mc_event_featured" value="1" <?php checked('1', $event_featured); ?> /> <?php esc_html_e('Yes', 'mc-intranet-core'); ?></label></td>
      </tr>
      <tr>
        <th><label for="mc_event_gallery_ids"><?php esc_html_e('Image Gallery', 'mc-intranet-core'); ?></label></th>
        <td>
          <input type="hidden" id="mc_event_gallery_ids" name="mc_event_gallery_ids" value="<?php echo esc_attr($event_gallery_ids); ?>" />
          <div id="mc-event-gallery-preview" style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:12px;">
            <?php foreach ($gallery_ids as $attachment_id) :
              $thumb_url = wp_get_attachment_image_url($attachment_id, 'thumbnail');
              if (! $thumb_url) {
                continue;
              }
            ?>
              <img src="<?php echo esc_url($thumb_url); ?>" alt="" style="width:72px;height:72px;object-fit:cover;border-radius:8px;border:1px solid #d0d5dd;" />
            <?php endforeach; ?>
          </div>
          <p>
            <button type="button" class="button" id="mc-event-gallery-select"><?php esc_html_e('Seleccionar galería', 'mc-intranet-core'); ?></button>
            <button type="button" class="button button-secondary" id="mc-event-gallery-clear"><?php esc_html_e('Limpiar', 'mc-intranet-core'); ?></button>
          </p>
          <p class="description"><?php esc_html_e('La primera imagen se usa como principal y las siguientes se muestran en el mosaico del shortcode.', 'mc-intranet-core'); ?></p>
        </td>
      </tr>
    </table>
    <script>
      (function() {
        const initGalleryField = () => {
          const selectButton = document.getElementById('mc-event-gallery-select');
          const clearButton = document.getElementById('mc-event-gallery-clear');
          const input = document.getElementById('mc_event_gallery_ids');
          const preview = document.getElementById('mc-event-gallery-preview');

          if (!selectButton || !clearButton || !input || !preview) {
            return;
          }

          if (selectButton.dataset.galleryReady === '1') {
            return;
          }

          if (typeof wp === 'undefined' || !wp.media) {
            window.setTimeout(initGalleryField, 120);
            return;
          }

          let frame;

          const renderPreview = (attachments) => {
            preview.innerHTML = '';

            attachments.forEach((attachment) => {
              const imageUrl = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
              const image = document.createElement('img');
              image.src = imageUrl;
              image.alt = '';
              image.style.width = '72px';
              image.style.height = '72px';
              image.style.objectFit = 'cover';
              image.style.borderRadius = '8px';
              image.style.border = '1px solid #d0d5dd';
              preview.appendChild(image);
            });
          };

          selectButton.dataset.galleryReady = '1';

          selectButton.addEventListener('click', () => {
            if (!frame) {
              frame = wp.media({
                title: '<?php echo esc_js(__('Seleccionar galería del evento', 'mc-intranet-core')); ?>',
                button: {
                  text: '<?php echo esc_js(__('Usar imágenes', 'mc-intranet-core')); ?>'
                },
                multiple: true,
                library: {
                  type: 'image'
                }
              });

              frame.on('select', () => {
                const attachments = frame.state().get('selection').map((item) => item.toJSON());
                input.value = attachments.map((attachment) => attachment.id).join(',');
                renderPreview(attachments);
              });
            }

            frame.open();
          });

          clearButton.addEventListener('click', () => {
            input.value = '';
            preview.innerHTML = '';
          });
        };

        if (document.readyState === 'loading') {
          document.addEventListener('DOMContentLoaded', initGalleryField);
        } else {
          initGalleryField();
        }
      }());
    </script>
  <?php
  }

  public function render_reconocimiento_meta_box(WP_Post $post): void
  {
  ?>
    <p style="color:#555;font-size:13px;line-height:1.6;margin:8px 0;">
      <?php esc_html_e( 'Usa el Título como nombre de la persona, la Imagen Destacada para su foto y el Editor para la descripción del reconocimiento.', 'mc-intranet-core' ); ?>
    </p>
  <?php
  }

  public function render_sede_meta_box(WP_Post $post): void
  {
    wp_nonce_field('mc_intranet_meta_boxes_action', 'mc_intranet_meta_boxes_nonce');

    $company_label = (string) get_post_meta($post->ID, 'company_label', true);
    $address_full  = (string) get_post_meta($post->ID, 'address_full', true);
    $maps_url      = (string) get_post_meta($post->ID, 'maps_url', true);
    $logo_id       = absint((string) get_post_meta($post->ID, 'sede_logo_id', true));
    $logo_url      = $logo_id > 0 ? wp_get_attachment_image_url($logo_id, 'thumbnail') : '';
    $font_color    = (string) get_post_meta($post->ID, 'sede_font_color', true);

  ?>
    <table class="form-table" role="presentation">
      <tr>
        <th><label for="mc_company_label"><?php esc_html_e('Company Label', 'mc-intranet-core'); ?></label></th>
        <td><input type="text" id="mc_company_label" name="mc_company_label" class="regular-text" value="<?php echo esc_attr($company_label); ?>" /></td>
      </tr>
      <tr>
        <th><label for="mc_address_full"><?php esc_html_e('Address', 'mc-intranet-core'); ?></label></th>
        <td><textarea id="mc_address_full" name="mc_address_full" class="large-text" rows="3"><?php echo esc_textarea($address_full); ?></textarea></td>
      </tr>
      <tr>
        <th><label for="mc_maps_url"><?php esc_html_e('Maps URL', 'mc-intranet-core'); ?></label></th>
        <td><input type="url" id="mc_maps_url" name="mc_maps_url" class="regular-text" value="<?php echo esc_attr($maps_url); ?>" /></td>
      </tr>
      <tr>
        <th><label><?php esc_html_e('Color de fuente', 'mc-intranet-core'); ?></label></th>
        <td>
          <input type="hidden" id="mc_sede_font_color" name="mc_sede_font_color" value="<?php echo esc_attr($font_color); ?>" />
          <input type="color" id="mc_sede_font_color_picker" value="<?php echo esc_attr($font_color ?: '#ffffff'); ?>" <?php echo $font_color ? '' : 'disabled'; ?> />
          <label style="margin-left:8px;">
            <input type="checkbox" id="mc_sede_font_color_active" <?php checked((bool) $font_color); ?> />
            <?php esc_html_e('Personalizar', 'mc-intranet-core'); ?>
          </label>
          <p class="description"><?php esc_html_e('Color del texto (nombre y dirección) en la tarjeta de sede. Desactiva para usar los colores del tema.', 'mc-intranet-core'); ?></p>
        </td>
      </tr>
      <tr>
        <th><label for="mc_sede_logo_id"><?php esc_html_e('Logo de Sede', 'mc-intranet-core'); ?></label></th>
        <td>
          <input type="hidden" id="mc_sede_logo_id" name="mc_sede_logo_id" value="<?php echo esc_attr((string) $logo_id); ?>" />
          <div id="mc-sede-logo-preview" style="margin-bottom:12px;">
            <?php if ($logo_url) : ?>
              <img src="<?php echo esc_url($logo_url); ?>" alt="" style="width:72px;height:72px;object-fit:contain;border-radius:8px;border:1px solid #d0d5dd;background:#fff;padding:8px;" />
            <?php endif; ?>
          </div>
          <p>
            <button type="button" class="button" id="mc-sede-logo-select"><?php esc_html_e('Seleccionar logo', 'mc-intranet-core'); ?></button>
            <button type="button" class="button button-secondary" id="mc-sede-logo-clear"><?php esc_html_e('Quitar logo', 'mc-intranet-core'); ?></button>
          </p>
          <p class="description"><?php esc_html_e('Logo específico para esta sede. Si está vacío, el shortcode usará el logo de empresa por defecto.', 'mc-intranet-core'); ?></p>
        </td>
      </tr>
    </table>
    <script>
      (function() {
        const initSedeLogoField = () => {
          const selectButton = document.getElementById('mc-sede-logo-select');
          const clearButton = document.getElementById('mc-sede-logo-clear');
          const input = document.getElementById('mc_sede_logo_id');
          const preview = document.getElementById('mc-sede-logo-preview');

          if (!selectButton || !clearButton || !input || !preview) {
            return;
          }

          if (selectButton.dataset.logoReady === '1') {
            return;
          }

          if (typeof wp === 'undefined' || !wp.media) {
            window.setTimeout(initSedeLogoField, 120);
            return;
          }

          let frame;

          const renderPreview = (imageUrl) => {
            preview.innerHTML = '';

            if (!imageUrl) {
              return;
            }

            const image = document.createElement('img');
            image.src = imageUrl;
            image.alt = '';
            image.style.width = '72px';
            image.style.height = '72px';
            image.style.objectFit = 'contain';
            image.style.borderRadius = '8px';
            image.style.border = '1px solid #d0d5dd';
            image.style.background = '#fff';
            image.style.padding = '8px';
            preview.appendChild(image);
          };

          selectButton.dataset.logoReady = '1';

          selectButton.addEventListener('click', () => {
            if (!frame) {
              frame = wp.media({
                title: '<?php echo esc_js(__('Seleccionar logo de sede', 'mc-intranet-core')); ?>',
                button: {
                  text: '<?php echo esc_js(__('Usar logo', 'mc-intranet-core')); ?>'
                },
                multiple: false,
                library: {
                  type: 'image'
                }
              });

              frame.on('select', () => {
                const attachment = frame.state().get('selection').first();
                if (!attachment) {
                  return;
                }

                const data = attachment.toJSON();
                input.value = data.id || '';
                const imageUrl = data.sizes && data.sizes.thumbnail ? data.sizes.thumbnail.url : data.url;
                renderPreview(imageUrl || '');
              });
            }

            frame.open();
          });

          clearButton.addEventListener('click', () => {
            input.value = '';
            renderPreview('');
          });
        };

        const initSedeFontColorField = () => {
          const hidden  = document.getElementById('mc_sede_font_color');
          const picker  = document.getElementById('mc_sede_font_color_picker');
          const checkbox = document.getElementById('mc_sede_font_color_active');

          if (!hidden || !picker || !checkbox) {
            return;
          }

          const syncPickerState = () => {
            picker.disabled = !checkbox.checked;
            hidden.value = checkbox.checked ? picker.value : '';
          };

          checkbox.addEventListener('change', syncPickerState);
          picker.addEventListener('input', () => {
            if (checkbox.checked) {
              hidden.value = picker.value;
            }
          });

          syncPickerState();
        };

        if (document.readyState === 'loading') {
          document.addEventListener('DOMContentLoaded', () => {
            initSedeLogoField();
            initSedeFontColorField();
          });
        } else {
          initSedeLogoField();
          initSedeFontColorField();
        }
      }());
    </script>
  <?php
  }

  public function render_directorio_contactos_meta_box(WP_Post $post): void
  {
    wp_nonce_field('mc_intranet_meta_boxes_action', 'mc_intranet_meta_boxes_nonce');

    $area            = (string) get_post_meta($post->ID, 'area', true);
    $cargo           = (string) get_post_meta($post->ID, 'cargo', true);
    $nombre          = (string) get_post_meta($post->ID, 'nombre', true);
    $celular         = (string) get_post_meta($post->ID, 'celular', true);
    $email           = (string) get_post_meta($post->ID, 'email', true);
    $company_context = (string) get_post_meta($post->ID, 'company_context', true);

    $allowed_companies = ['mc', 'anstra', 'essenza', 'budefry', 'interactua'];
  ?>
    <table class="form-table" role="presentation">
      <tr>
        <th><label for="mc_dc_company_context"><?php esc_html_e('Empresa (Company Context)', 'mc-intranet-core'); ?></label></th>
        <td>
          <select id="mc_dc_company_context" name="mc_dc_company_context">
            <option value=""><?php esc_html_e('— Seleccionar —', 'mc-intranet-core'); ?></option>
            <?php foreach ($allowed_companies as $slug) : ?>
              <option value="<?php echo esc_attr($slug); ?>" <?php selected($company_context, $slug); ?>><?php echo esc_html($slug); ?></option>
            <?php endforeach; ?>
          </select>
        </td>
      </tr>
      <tr>
        <th><label for="mc_dc_area"><?php esc_html_e('Área', 'mc-intranet-core'); ?></label></th>
        <td><input type="text" id="mc_dc_area" name="mc_dc_area" class="regular-text" value="<?php echo esc_attr($area); ?>" /></td>
      </tr>
      <tr>
        <th><label for="mc_dc_cargo"><?php esc_html_e('Cargo', 'mc-intranet-core'); ?></label></th>
        <td><input type="text" id="mc_dc_cargo" name="mc_dc_cargo" class="regular-text" value="<?php echo esc_attr($cargo); ?>" /></td>
      </tr>
      <tr>
        <th><label for="mc_dc_nombre"><?php esc_html_e('Nombre', 'mc-intranet-core'); ?></label></th>
        <td><input type="text" id="mc_dc_nombre" name="mc_dc_nombre" class="regular-text" value="<?php echo esc_attr($nombre); ?>" /></td>
      </tr>
      <tr>
        <th><label for="mc_dc_celular"><?php esc_html_e('Celular', 'mc-intranet-core'); ?></label></th>
        <td><input type="tel" id="mc_dc_celular" name="mc_dc_celular" class="regular-text" value="<?php echo esc_attr($celular); ?>" /></td>
      </tr>
      <tr>
        <th><label for="mc_dc_email"><?php esc_html_e('Email', 'mc-intranet-core'); ?></label></th>
        <td><input type="email" id="mc_dc_email" name="mc_dc_email" class="regular-text" value="<?php echo esc_attr($email); ?>" /></td>
      </tr>
    </table>
<?php
  }

  public function render_company_portal_meta_box(WP_Post $post): void
  {
    wp_nonce_field('mc_intranet_meta_boxes_action', 'mc_intranet_meta_boxes_nonce');

    $slug              = (string) get_post_meta($post->ID, 'portal_slug', true);
    $name              = (string) get_post_meta($post->ID, 'portal_name', true);
    $desc              = (string) get_post_meta($post->ID, 'portal_desc', true);
    $color_start       = (string) get_post_meta($post->ID, 'portal_color_start', true);
    $color_end         = (string) get_post_meta($post->ID, 'portal_color_end', true);
    $link_color        = (string) get_post_meta($post->ID, 'portal_link_color', true);
    $header_bg_color   = (string) get_post_meta($post->ID, 'portal_header_bg_color', true);
    $header_text_color = (string) get_post_meta($post->ID, 'portal_header_text_color', true);
    $url               = (string) get_post_meta($post->ID, 'portal_url', true);
    $tags              = (string) get_post_meta($post->ID, 'portal_tags', true);
    $count_label       = (string) get_post_meta($post->ID, 'portal_count_label', true);

?>
    <table class="form-table" role="presentation">
      <tr>
        <th><label for="mc_portal_slug"><?php esc_html_e('Slug', 'mc-intranet-core'); ?></label></th>
        <td>
          <input type="text" id="mc_portal_slug" name="mc_portal_slug" class="regular-text" value="<?php echo esc_attr($slug); ?>" />
          <p class="description"><?php esc_html_e('Ejemplo: anstra, essenza, budefry, interactua.', 'mc-intranet-core'); ?></p>
        </td>
      </tr>
      <tr>
        <th><label for="mc_portal_name"><?php esc_html_e('Nombre', 'mc-intranet-core'); ?></label></th>
        <td><input type="text" id="mc_portal_name" name="mc_portal_name" class="regular-text" value="<?php echo esc_attr($name); ?>" /></td>
      </tr>
      <tr>
        <th><label for="mc_portal_desc"><?php esc_html_e('Descripción', 'mc-intranet-core'); ?></label></th>
        <td><textarea id="mc_portal_desc" name="mc_portal_desc" class="large-text" rows="3"><?php echo esc_textarea($desc); ?></textarea></td>
      </tr>
      <tr>
        <th><label for="mc_portal_color_start"><?php esc_html_e('Color Start', 'mc-intranet-core'); ?></label></th>
        <td><input type="text" id="mc_portal_color_start" name="mc_portal_color_start" class="regular-text" value="<?php echo esc_attr($color_start); ?>" placeholder="#1A2E52" /></td>
      </tr>
      <tr>
        <th><label for="mc_portal_color_end"><?php esc_html_e('Color End', 'mc-intranet-core'); ?></label></th>
        <td><input type="text" id="mc_portal_color_end" name="mc_portal_color_end" class="regular-text" value="<?php echo esc_attr($color_end); ?>" placeholder="#253E6E" /></td>
      </tr>
      <tr>
        <th><label for="mc_portal_link_color"><?php esc_html_e('Link Color', 'mc-intranet-core'); ?></label></th>
        <td><input type="text" id="mc_portal_link_color" name="mc_portal_link_color" class="regular-text" value="<?php echo esc_attr($link_color); ?>" placeholder="#1A2E52" /></td>
      </tr>
      <tr>
        <th><label for="mc_portal_header_bg_color"><?php esc_html_e('Header BG Color', 'mc-intranet-core'); ?></label></th>
        <td><input type="text" id="mc_portal_header_bg_color" name="mc_portal_header_bg_color" class="regular-text" value="<?php echo esc_attr($header_bg_color); ?>" placeholder="#FFFFFF" /></td>
      </tr>
      <tr>
        <th><label for="mc_portal_header_text_color"><?php esc_html_e('Header Text Color', 'mc-intranet-core'); ?></label></th>
        <td><input type="text" id="mc_portal_header_text_color" name="mc_portal_header_text_color" class="regular-text" value="<?php echo esc_attr($header_text_color); ?>" placeholder="#0F172A" /></td>
      </tr>
      <tr>
        <th><label for="mc_portal_url"><?php esc_html_e('URL', 'mc-intranet-core'); ?></label></th>
        <td><input type="url" id="mc_portal_url" name="mc_portal_url" class="regular-text" value="<?php echo esc_attr($url); ?>" /></td>
      </tr>
      <tr>
        <th><label for="mc_portal_tags"><?php esc_html_e('Tags', 'mc-intranet-core'); ?></label></th>
        <td>
          <input type="text" id="mc_portal_tags" name="mc_portal_tags" class="regular-text" value="<?php echo esc_attr($tags); ?>" />
          <p class="description"><?php esc_html_e('Separadas por coma. Ejemplo: RRHH, Contabilidad, Administración.', 'mc-intranet-core'); ?></p>
        </td>
      </tr>
      <tr>
        <th><label for="mc_portal_count_label"><?php esc_html_e('Count Label', 'mc-intranet-core'); ?></label></th>
        <td><input type="text" id="mc_portal_count_label" name="mc_portal_count_label" class="regular-text" value="<?php echo esc_attr($count_label); ?>" /></td>
      </tr>
    </table>
<?php
  }

  public function save_meta_boxes(int $post_id, WP_Post $post): void
  {
    if (! isset($_POST['mc_intranet_meta_boxes_nonce'])) {
      return;
    }

    $nonce = sanitize_text_field(wp_unslash($_POST['mc_intranet_meta_boxes_nonce']));
    if (! wp_verify_nonce($nonce, 'mc_intranet_meta_boxes_action')) {
      return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
      return;
    }

    if (wp_is_post_revision($post_id)) {
      return;
    }

    if (! current_user_can('edit_post', $post_id)) {
      return;
    }

    switch ($post->post_type) {
      case 'mc_formulario':
        $this->save_text_field($post_id, 'company_context', 'mc_company_context', 'sanitize_key');
        $this->save_text_field($post_id, 'area_context', 'mc_area_context', 'sanitize_key');
        $this->save_text_field($post_id, 'form_type', 'mc_form_type', 'sanitize_key');
        $this->save_text_field($post_id, 'form_url', 'mc_form_url', 'esc_url_raw');
        $this->save_text_field($post_id, 'cta_label', 'mc_cta_label', 'sanitize_text_field');
        $this->save_checkbox_field($post_id, 'open_new_tab', 'mc_open_new_tab');
        $this->save_checkbox_field($post_id, 'is_featured', 'mc_is_featured');
        $this->save_number_field($post_id, 'order_weight', 'mc_order_weight');
        break;

      case 'mc_evento':
        $this->save_date_field($post_id, 'event_date', 'mc_event_date');
        $this->save_text_field($post_id, 'event_mode', 'mc_event_mode', 'sanitize_key');
        $this->save_text_field($post_id, 'event_location', 'mc_event_location', 'sanitize_text_field');
        $this->save_text_field($post_id, 'company_context', 'mc_event_company_context', 'sanitize_key');
        $this->save_checkbox_field($post_id, 'event_featured', 'mc_event_featured');
        $this->save_gallery_field($post_id, 'event_gallery_ids', 'mc_event_gallery_ids');
        break;

      case 'mc_reconocimiento':
        // Título, imagen destacada y descripción se gestionan por campos nativos de WordPress.
        break;

      case 'mc_sede':
        $this->save_text_field($post_id, 'company_label', 'mc_company_label', 'sanitize_text_field');
        $this->save_textarea_field($post_id, 'address_full', 'mc_address_full');
        $this->save_text_field($post_id, 'maps_url', 'mc_maps_url', 'esc_url_raw');
        $this->save_attachment_id_field($post_id, 'sede_logo_id', 'mc_sede_logo_id');
        $this->save_hex_color_field($post_id, 'sede_font_color', 'mc_sede_font_color');
        break;

      case 'mc_directorio':
        $allowed = ['mc', 'anstra', 'essenza', 'budefry', 'interactua'];
        $company = isset($_POST['mc_dc_company_context'])
          ? sanitize_key(wp_unslash($_POST['mc_dc_company_context']))
          : '';
        if (in_array($company, $allowed, true)) {
          update_post_meta($post_id, 'company_context', $company);
        } else {
          delete_post_meta($post_id, 'company_context');
        }
        $this->save_text_field($post_id, 'area',    'mc_dc_area',    'sanitize_text_field');
        $this->save_text_field($post_id, 'cargo',   'mc_dc_cargo',   'sanitize_text_field');
        $this->save_text_field($post_id, 'nombre',  'mc_dc_nombre',  'sanitize_text_field');
        $this->save_text_field($post_id, 'celular', 'mc_dc_celular', 'sanitize_text_field');
        $this->save_text_field($post_id, 'email',   'mc_dc_email',   'sanitize_email');
        break;

      case 'mc_company_portal':
        $this->save_text_field($post_id, 'portal_slug', 'mc_portal_slug', 'sanitize_key');
        $this->save_text_field($post_id, 'portal_name', 'mc_portal_name', 'sanitize_text_field');
        $this->save_textarea_field($post_id, 'portal_desc', 'mc_portal_desc');
        $this->save_hex_color_field($post_id, 'portal_color_start', 'mc_portal_color_start');
        $this->save_hex_color_field($post_id, 'portal_color_end', 'mc_portal_color_end');
        $this->save_hex_color_field($post_id, 'portal_link_color', 'mc_portal_link_color');
        $this->save_hex_color_field($post_id, 'portal_header_bg_color', 'mc_portal_header_bg_color');
        $this->save_hex_color_field($post_id, 'portal_header_text_color', 'mc_portal_header_text_color');
        $this->save_text_field($post_id, 'portal_url', 'mc_portal_url', 'esc_url_raw');
        $this->save_csv_list_field($post_id, 'portal_tags', 'mc_portal_tags');
        $this->save_text_field($post_id, 'portal_count_label', 'mc_portal_count_label', 'sanitize_text_field');
        break;
    }
  }

  private function save_text_field(int $post_id, string $meta_key, string $post_key, callable $sanitizer): void
  {
    if (! isset($_POST[$post_key])) {
      return;
    }

    $value = call_user_func($sanitizer, wp_unslash($_POST[$post_key]));

    if ('' === $value) {
      delete_post_meta($post_id, $meta_key);
      return;
    }

    update_post_meta($post_id, $meta_key, $value);
  }

  private function save_textarea_field(int $post_id, string $meta_key, string $post_key): void
  {
    if (! isset($_POST[$post_key])) {
      return;
    }

    $value = sanitize_textarea_field(wp_unslash($_POST[$post_key]));

    if ('' === $value) {
      delete_post_meta($post_id, $meta_key);
      return;
    }

    update_post_meta($post_id, $meta_key, $value);
  }

  private function save_checkbox_field(int $post_id, string $meta_key, string $post_key): void
  {
    $value = isset($_POST[$post_key]) ? '1' : '0';
    update_post_meta($post_id, $meta_key, $value);
  }

  private function save_number_field(int $post_id, string $meta_key, string $post_key): void
  {
    if (! isset($_POST[$post_key])) {
      return;
    }

    $value = absint(wp_unslash($_POST[$post_key]));
    update_post_meta($post_id, $meta_key, (string) $value);
  }

  private function save_date_field(int $post_id, string $meta_key, string $post_key): void
  {
    if (! isset($_POST[$post_key])) {
      return;
    }

    $value = sanitize_text_field(wp_unslash($_POST[$post_key]));

    if ('' === $value) {
      delete_post_meta($post_id, $meta_key);
      return;
    }

    if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
      return;
    }

    update_post_meta($post_id, $meta_key, $value);
  }

  private function save_gallery_field(int $post_id, string $meta_key, string $post_key): void
  {
    if (! isset($_POST[$post_key])) {
      return;
    }

    $raw_value = sanitize_text_field(wp_unslash($_POST[$post_key]));
    $ids = array_values(array_filter(array_map('absint', explode(',', $raw_value))));

    if ([] === $ids) {
      delete_post_meta($post_id, $meta_key);
      return;
    }

    update_post_meta($post_id, $meta_key, implode(',', $ids));
  }

  private function save_attachment_id_field(int $post_id, string $meta_key, string $post_key): void
  {
    if (! isset($_POST[$post_key])) {
      return;
    }

    $value = absint(wp_unslash($_POST[$post_key]));

    if (0 === $value) {
      delete_post_meta($post_id, $meta_key);
      return;
    }

    update_post_meta($post_id, $meta_key, (string) $value);
  }

  private function save_hex_color_field(int $post_id, string $meta_key, string $post_key): void
  {
    if (! isset($_POST[$post_key])) {
      return;
    }

    $value = sanitize_hex_color(wp_unslash($_POST[$post_key]));

    if ('' === $value || null === $value) {
      delete_post_meta($post_id, $meta_key);
      return;
    }

    update_post_meta($post_id, $meta_key, $value);
  }

  private function save_csv_list_field(int $post_id, string $meta_key, string $post_key): void
  {
    if (! isset($_POST[$post_key])) {
      return;
    }

    $raw_items = explode(',', sanitize_text_field(wp_unslash($_POST[$post_key])));
    $clean_items = [];

    foreach ($raw_items as $item) {
      $item = trim($item);

      if ('' === $item) {
        continue;
      }

      $clean_items[] = $item;
    }

    if ([] === $clean_items) {
      delete_post_meta($post_id, $meta_key);
      return;
    }

    update_post_meta($post_id, $meta_key, implode(', ', $clean_items));
  }

  private function save_initials_field(int $post_id, string $meta_key, string $post_key): void
  {
    if (! isset($_POST[$post_key])) {
      return;
    }

    $value = sanitize_text_field(wp_unslash($_POST[$post_key]));
    $value = strtoupper(substr($value, 0, 3));

    if ('' === $value) {
      delete_post_meta($post_id, $meta_key);
      return;
    }

    update_post_meta($post_id, $meta_key, $value);
  }
}
