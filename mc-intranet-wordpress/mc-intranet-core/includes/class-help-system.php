<?php
/**
 * Sistema de ayudas para administradores — MC Intranet Core.
 *
 * Registra una página de administración con guías paso a paso
 * para editar cada CPT y modificar las páginas del sitio.
 *
 * @package MC_Intranet_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MC_Intranet_Help_System {

    public function __construct() {
        add_action( 'admin_menu', [ $this, 'register_menu' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
    }

    public function register_menu(): void {
        add_menu_page(
            __( 'Guía de la Intranet', 'mc-intranet-core' ),
            __( 'Ayuda / Guía', 'mc-intranet-core' ),
            'edit_posts',
            'mc-intranet-help',
            [ $this, 'render_page' ],
            'dashicons-book-alt',
            99
        );
    }

    public function enqueue_assets( string $hook_suffix ): void {
        if ( 'toplevel_page_mc-intranet-help' !== $hook_suffix ) {
            return;
        }

        wp_add_inline_style( 'wp-admin', $this->get_styles() );
        wp_add_inline_script( 'jquery', $this->get_scripts() );
    }

    private function get_styles(): string {
        return '
        /* ── MC Help System ────────────────────────────────── */
        .mc-help-wrap { max-width: 1000px; margin: 20px 0; }
        .mc-help-wrap h1 { font-size: 22px; font-weight: 700; margin-bottom: 4px; }
        .mc-help-wrap .mc-help-intro { color: #666; margin-bottom: 24px; font-size: 14px; }

        /* Nav tabs */
        .mc-help-tabs { display: flex; flex-wrap: wrap; gap: 4px; border-bottom: 2px solid #dcdcde; margin-bottom: 28px; }
        .mc-help-tab-btn {
            background: none; border: none; padding: 9px 16px; cursor: pointer;
            font-size: 13px; font-weight: 600; color: #555; border-radius: 4px 4px 0 0;
            border-bottom: 2px solid transparent; margin-bottom: -2px; transition: color .15s;
        }
        .mc-help-tab-btn:hover { color: #2271b1; background: #f0f6fc; }
        .mc-help-tab-btn.is-active { color: #2271b1; border-bottom-color: #2271b1; background: #fff; }

        /* Panels */
        .mc-help-panel { display: none; }
        .mc-help-panel.is-active { display: block; }

        /* Section card */
        .mc-help-card {
            background: #fff; border: 1px solid #dcdcde; border-radius: 8px;
            padding: 22px 26px; margin-bottom: 20px;
        }
        .mc-help-card h2 { font-size: 17px; font-weight: 700; margin: 0 0 6px; color: #1d2327; }
        .mc-help-card h3 { font-size: 14px; font-weight: 700; margin: 18px 0 6px; color: #1d2327; }
        .mc-help-card p { font-size: 13px; line-height: 1.7; color: #444; margin: 0 0 10px; }
        .mc-help-card ul, .mc-help-card ol { font-size: 13px; line-height: 1.7; color: #444; padding-left: 20px; margin: 0 0 12px; }
        .mc-help-card li { margin-bottom: 4px; }
        .mc-help-card code {
            background: #f0f0f1; padding: 1px 6px; border-radius: 3px;
            font-size: 12px; color: #c0392b; font-family: monospace;
        }

        /* Field table */
        .mc-help-fields { width: 100%; border-collapse: collapse; font-size: 13px; margin: 10px 0 16px; }
        .mc-help-fields th { background: #f6f7f7; text-align: left; padding: 8px 12px; font-weight: 700; color: #1d2327; border: 1px solid #dcdcde; }
        .mc-help-fields td { padding: 8px 12px; border: 1px solid #dcdcde; vertical-align: top; line-height: 1.6; color: #444; }
        .mc-help-fields td:first-child { font-family: monospace; font-size: 12px; color: #c0392b; white-space: nowrap; }
        .mc-help-fields td:nth-child(2) { font-weight: 600; color: #1d2327; }
        .mc-help-fields tr:hover td { background: #f9f9f9; }

        /* Shortcode block */
        .mc-help-shortcode {
            background: #f6f7f7; border-left: 3px solid #2271b1; border-radius: 0 4px 4px 0;
            padding: 10px 14px; margin: 8px 0 16px; font-family: monospace; font-size: 13px; color: #1d2327;
        }

        /* Step list */
        .mc-help-steps { counter-reset: step-counter; list-style: none; padding: 0; margin: 0 0 16px; }
        .mc-help-steps li {
            counter-increment: step-counter; position: relative;
            padding: 6px 6px 6px 40px; font-size: 13px; line-height: 1.6; color: #444;
        }
        .mc-help-steps li::before {
            content: counter(step-counter);
            position: absolute; left: 0; top: 5px;
            background: #2271b1; color: #fff; width: 24px; height: 24px;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 700;
        }

        /* Badge */
        .mc-help-badge {
            display: inline-block; padding: 2px 8px; border-radius: 10px;
            font-size: 11px; font-weight: 600; margin-left: 8px; vertical-align: middle;
        }
        .mc-help-badge--required { background: #fde8e8; color: #c0392b; }
        .mc-help-badge--optional { background: #e8f4fd; color: #2271b1; }

        /* Index grid */
        .mc-help-index { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 12px; margin: 16px 0; }
        .mc-help-index-item {
            background: #f6f7f7; border: 1px solid #dcdcde; border-radius: 6px;
            padding: 14px 16px; cursor: pointer; transition: box-shadow .15s, border-color .15s;
        }
        .mc-help-index-item:hover { border-color: #2271b1; box-shadow: 0 0 0 1px #2271b1; }
        .mc-help-index-item .dashicons { color: #2271b1; font-size: 22px; width: 22px; height: 22px; margin-bottom: 6px; }
        .mc-help-index-item strong { display: block; font-size: 13px; color: #1d2327; }
        .mc-help-index-item span { font-size: 12px; color: #666; }

        /* Tip box */
        .mc-help-tip {
            background: #eef6fd; border: 1px solid #bcdaf5; border-radius: 6px;
            padding: 10px 14px; font-size: 13px; color: #1d2327; margin: 12px 0;
            display: flex; gap: 10px; align-items: flex-start;
        }
        .mc-help-tip .dashicons { color: #2271b1; flex-shrink: 0; margin-top: 1px; }

        .mc-help-warning {
            background: #fef9e7; border: 1px solid #f5cba7; border-radius: 6px;
            padding: 10px 14px; font-size: 13px; color: #7d6608; margin: 12px 0;
            display: flex; gap: 10px; align-items: flex-start;
        }
        .mc-help-warning .dashicons { color: #c0392b; flex-shrink: 0; margin-top: 1px; }
        ';
    }

    private function get_scripts(): string {
        return '
        jQuery(function($) {
            $(document).on("click", ".mc-help-tab-btn", function() {
                var target = $(this).data("tab");
                $(".mc-help-tab-btn").removeClass("is-active");
                $(".mc-help-panel").removeClass("is-active");
                $(this).addClass("is-active");
                $("#mc-help-panel-" + target).addClass("is-active");
                window.location.hash = target;
            });
            $(document).on("click", ".mc-help-index-item", function() {
                var tab = $(this).data("tab");
                $(".mc-help-tab-btn[data-tab=\'" + tab + "\']").trigger("click");
                $("html, body").animate({ scrollTop: 0 }, 200);
            });
            // Activate tab from hash on load
            var hash = window.location.hash.replace("#", "");
            if (hash && $(".mc-help-tab-btn[data-tab=\'" + hash + "\']").length) {
                $(".mc-help-tab-btn[data-tab=\'" + hash + "\']").trigger("click");
            } else {
                $(".mc-help-tab-btn").first().trigger("click");
            }
        });
        ';
    }

    public function render_page(): void {
        if ( ! current_user_can( 'edit_posts' ) ) {
            return;
        }
        ?>
        <div class="wrap mc-help-wrap">
            <h1><?php esc_html_e( 'Guía de administración · MC Intranet', 'mc-intranet-core' ); ?></h1>
            <p class="mc-help-intro"><?php esc_html_e( 'Referencia completa para gestionar contenidos: cómo editar cada tipo de contenido, qué significa cada campo y cómo se usan los shortcodes en las páginas.', 'mc-intranet-core' ); ?></p>

            <div class="mc-help-tabs">
                <button class="mc-help-tab-btn" data-tab="inicio">Inicio</button>
                <button class="mc-help-tab-btn" data-tab="formularios">Formularios</button>
                <button class="mc-help-tab-btn" data-tab="eventos">Eventos</button>
                <button class="mc-help-tab-btn" data-tab="reconocimientos">Reconocimientos</button>
                <button class="mc-help-tab-btn" data-tab="sedes">Sedes</button>
                <button class="mc-help-tab-btn" data-tab="directorio">Directorio</button>
                <button class="mc-help-tab-btn" data-tab="portales">Portales</button>
                <button class="mc-help-tab-btn" data-tab="shortcodes">Shortcodes</button>
                <button class="mc-help-tab-btn" data-tab="branding">Branding</button>
                <button class="mc-help-tab-btn" data-tab="paginas">Páginas del sitio</button>
            </div>

            <?php $this->render_panel_inicio(); ?>
            <?php $this->render_panel_formularios(); ?>
            <?php $this->render_panel_eventos(); ?>
            <?php $this->render_panel_reconocimientos(); ?>
            <?php $this->render_panel_sedes(); ?>
            <?php $this->render_panel_directorio(); ?>
            <?php $this->render_panel_portales(); ?>
            <?php $this->render_panel_shortcodes(); ?>
            <?php $this->render_panel_branding(); ?>
            <?php $this->render_panel_paginas(); ?>
        </div>
        <?php
    }

    // ─── Panel: Inicio ──────────────────────────────────────────────────────────

    private function render_panel_inicio(): void {
        ?>
        <div id="mc-help-panel-inicio" class="mc-help-panel">
            <div class="mc-help-card">
                <h2>Estructura del sistema</h2>
                <p>La intranet usa un plugin personalizado (<strong>MC Intranet Core</strong>) que define todos los tipos de contenido (CPT), taxonomías y shortcodes. Las páginas del sitio usan esos shortcodes para mostrar el contenido dinámicamente.</p>
                <p>Haz clic en cualquier sección para ver la guía detallada:</p>

                <div class="mc-help-index">
                    <div class="mc-help-index-item" data-tab="formularios">
                        <span class="dashicons dashicons-feedback"></span>
                        <strong>Formularios</strong>
                        <span>Documentos y enlaces a formularios internos</span>
                    </div>
                    <div class="mc-help-index-item" data-tab="eventos">
                        <span class="dashicons dashicons-calendar-alt"></span>
                        <strong>Eventos</strong>
                        <span>Eventos del portal Interactúa</span>
                    </div>
                    <div class="mc-help-index-item" data-tab="reconocimientos">
                        <span class="dashicons dashicons-awards"></span>
                        <strong>Reconocimientos</strong>
                        <span>Reconocimientos a colaboradores</span>
                    </div>
                    <div class="mc-help-index-item" data-tab="sedes">
                        <span class="dashicons dashicons-location"></span>
                        <strong>Sedes</strong>
                        <span>Ubicaciones en el footer</span>
                    </div>
                    <div class="mc-help-index-item" data-tab="directorio">
                        <span class="dashicons dashicons-id-alt"></span>
                        <strong>Directorio</strong>
                        <span>Directorio de contactos internos</span>
                    </div>
                    <div class="mc-help-index-item" data-tab="portales">
                        <span class="dashicons dashicons-grid-view"></span>
                        <strong>Portales</strong>
                        <span>Portales corporativos del inicio</span>
                    </div>
                    <div class="mc-help-index-item" data-tab="shortcodes">
                        <span class="dashicons dashicons-shortcode"></span>
                        <strong>Shortcodes</strong>
                        <span>Referencia completa de shortcodes</span>
                    </div>
                    <div class="mc-help-index-item" data-tab="branding">
                        <span class="dashicons dashicons-art"></span>
                        <strong>Branding</strong>
                        <span>Colores, logos y Hero por empresa</span>
                    </div>
                    <div class="mc-help-index-item" data-tab="paginas">
                        <span class="dashicons dashicons-admin-page"></span>
                        <strong>Páginas del sitio</strong>
                        <span>Cómo editar cada página</span>
                    </div>
                </div>
            </div>

            <div class="mc-help-card">
                <h2>Empresas del grupo</h2>
                <p>El sistema usa un <strong>slug de empresa</strong> para filtrar el contenido. Los slugs válidos son:</p>
                <table class="mc-help-fields">
                    <thead><tr><th>Slug</th><th>Empresa</th><th>Descripción</th></tr></thead>
                    <tbody>
                        <tr><td>mc</td><td>MC Group (matriz)</td><td>Contenido transversal a todas las empresas</td></tr>
                        <tr><td>anstra</td><td>Projection Anstra</td><td>NIT 901 967 530-0 · Admin, contabilidad, RRHH</td></tr>
                        <tr><td>essenza</td><td>Essenza Foods</td><td>NIT 901 971 854-7 · Comercial, mercadeo, marca</td></tr>
                        <tr><td>budefry</td><td>Budefry SAS</td><td>NIT 901 565 887-9 · Operación, logística, producción</td></tr>
                        <tr><td>interactua</td><td>Interactúa</td><td>Cultura corporativa, eventos, reconocimientos</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="mc-help-card">
                <h2>Taxonomías de clasificación</h2>
                <p>Los Formularios, Eventos y Reconocimientos se clasifican con dos taxonomías:</p>
                <table class="mc-help-fields">
                    <thead><tr><th>Taxonomía</th><th>Slugs disponibles</th><th>Aplica a</th></tr></thead>
                    <tbody>
                        <tr><td>mc_empresa</td><td>mc · anstra · essenza · budefry · interactua</td><td>Formularios, Eventos, Reconocimientos</td></tr>
                        <tr><td>mc_area</td><td>administracion · tic · gestiones · rrhh · cultura</td><td>Solo Formularios</td></tr>
                    </tbody>
                </table>
                <div class="mc-help-tip"><span class="dashicons dashicons-info"></span><span>Las taxonomías se asignan desde el panel lateral derecho al editar un Formulario, Evento o Reconocimiento.</span></div>
            </div>
        </div>
        <?php
    }

    // ─── Panel: Formularios ─────────────────────────────────────────────────────

    private function render_panel_formularios(): void {
        ?>
        <div id="mc-help-panel-formularios" class="mc-help-panel">
            <div class="mc-help-card">
                <h2>Formularios <code>mc_formulario</code></h2>
                <p>Representan enlaces a formularios internos (Google Forms, SharePoint, PDFs, etc.) o documentos de gestión. Se muestran en las páginas de cada empresa usando el shortcode <code>[mc_formularios]</code>.</p>

                <h3>Cómo agregar o editar un Formulario</h3>
                <ol class="mc-help-steps">
                    <li>En el menú lateral ve a <strong>Formularios → Agregar Formulario</strong> (o haz clic en uno existente para editarlo).</li>
                    <li>Escribe el <strong>Título</strong> del formulario (nombre visible en la tarjeta).</li>
                    <li>Escribe un <strong>Extracto</strong> con la descripción breve que aparece bajo el título.</li>
                    <li>Completa los campos del bloque <strong>"Datos del Formulario"</strong> (ver tabla abajo).</li>
                    <li>En el panel lateral asigna las taxonomías <strong>Empresa</strong> y <strong>Área</strong>.</li>
                    <li>Haz clic en <strong>Publicar</strong> o <strong>Actualizar</strong>.</li>
                </ol>

                <h3>Campos del bloque "Datos del Formulario"</h3>
                <table class="mc-help-fields">
                    <thead><tr><th>Campo interno</th><th>Etiqueta</th><th>Descripción</th></tr></thead>
                    <tbody>
                        <tr><td>company_context</td><td>Company Context</td><td>Slug de empresa al que pertenece (ej. <code>anstra</code>). Debe coincidir con la taxonomía Empresa asignada.</td></tr>
                        <tr><td>area_context</td><td>Area Context</td><td>Slug de área (ej. <code>rrhh</code>). Debe coincidir con la taxonomía Área asignada.</td></tr>
                        <tr><td>form_type</td><td>Form Type</td><td>Tipo de tarjeta: <code>form</code> (formulario) o <code>document</code> (documento). Afecta el ícono.</td></tr>
                        <tr><td>form_url</td><td>Form URL</td><td>URL de destino al hacer clic en la tarjeta (Google Form, SharePoint, PDF…).</td></tr>
                        <tr><td>cta_label</td><td>CTA Label</td><td>Texto del botón de acción. Default: "Abrir formulario".</td></tr>
                        <tr><td>open_new_tab</td><td>Open New Tab</td><td>Marcar si el enlace debe abrirse en una nueva pestaña.</td></tr>
                        <tr><td>is_featured</td><td>Featured</td><td>Marcar para mostrar el formulario destacado (se puede filtrar con <code>featured="yes"</code>).</td></tr>
                        <tr><td>order_weight</td><td>Order Weight</td><td>Número que controla el orden (menor número = aparece primero). Default: 0.</td></tr>
                    </tbody>
                </table>

                <div class="mc-help-tip"><span class="dashicons dashicons-info"></span><span>Si dos formularios tienen el mismo título, empresa y área, el sistema muestra solo el más relevante (evita duplicados automáticamente).</span></div>
                <div class="mc-help-warning"><span class="dashicons dashicons-warning"></span><span>El campo <strong>Company Context</strong> y la taxonomía <strong>Empresa</strong> deben tener el mismo slug para que el shortcode filtre correctamente.</span></div>
            </div>

            <div class="mc-help-card">
                <h2>Campos nativos de WordPress usados</h2>
                <table class="mc-help-fields">
                    <thead><tr><th>Campo</th><th>Uso</th></tr></thead>
                    <tbody>
                        <tr><td>Título</td><td>Nombre del formulario que aparece en la tarjeta</td></tr>
                        <tr><td>Extracto</td><td>Descripción breve visible bajo el título</td></tr>
                        <tr><td>Imagen destacada</td><td>Ícono o imagen de la tarjeta (opcional)</td></tr>
                        <tr><td>Orden del menú</td><td>Orden secundario si <code>order_weight</code> es igual entre varios</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }

    // ─── Panel: Eventos ─────────────────────────────────────────────────────────

    private function render_panel_eventos(): void {
        ?>
        <div id="mc-help-panel-eventos" class="mc-help-panel">
            <div class="mc-help-card">
                <h2>Eventos <code>mc_evento</code></h2>
                <p>Eventos corporativos del portal <strong>Interactúa</strong>. Se muestran en una línea de tiempo usando el shortcode <code>[mc_eventos]</code>.</p>

                <h3>Cómo agregar o editar un Evento</h3>
                <ol class="mc-help-steps">
                    <li>Ve a <strong>Eventos → Agregar Evento</strong>.</li>
                    <li>Escribe el <strong>Título</strong> del evento.</li>
                    <li>En el editor principal escribe la descripción completa del evento.</li>
                    <li>Completa los campos del bloque <strong>"Datos del Evento"</strong>.</li>
                    <li>Asigna la taxonomía <strong>Empresa</strong> en el panel lateral (normalmente <code>interactua</code>).</li>
                    <li>Publica el evento.</li>
                </ol>

                <h3>Campos del bloque "Datos del Evento"</h3>
                <table class="mc-help-fields">
                    <thead><tr><th>Campo interno</th><th>Etiqueta</th><th>Descripción</th></tr></thead>
                    <tbody>
                        <tr><td>event_date</td><td>Event Date</td><td>Fecha del evento en formato <code>AAAA-MM-DD</code>. Controla el orden en la línea de tiempo.</td></tr>
                        <tr><td>event_mode</td><td>Event Mode</td><td>Modalidad del evento (ej. <code>presencial</code>, <code>virtual</code>, <code>híbrido</code>).</td></tr>
                        <tr><td>event_location</td><td>Event Location</td><td>Lugar donde se realiza el evento.</td></tr>
                        <tr><td>company_context</td><td>Company Context</td><td>Slug de empresa (normalmente <code>interactua</code>).</td></tr>
                        <tr><td>event_featured</td><td>Featured Event</td><td>Marcar para destacar el evento en la línea de tiempo.</td></tr>
                        <tr><td>event_gallery_ids</td><td>Image Gallery</td><td>Galería de imágenes del evento. La primera imagen se usa como principal; las demás como mosaico.</td></tr>
                    </tbody>
                </table>

                <h3>Cómo agregar la galería de imágenes</h3>
                <ol class="mc-help-steps">
                    <li>En el bloque "Datos del Evento", haz clic en <strong>Seleccionar galería</strong>.</li>
                    <li>Se abre la biblioteca de medios. Selecciona una o varias imágenes (mantén Ctrl/Cmd para seleccionar varias).</li>
                    <li>Haz clic en <strong>Usar imágenes</strong>. Verás las miniaturas en el panel.</li>
                    <li>Para borrar la galería usa el botón <strong>Limpiar</strong>.</li>
                </ol>

                <div class="mc-help-tip"><span class="dashicons dashicons-info"></span><span>Si no hay galería, el shortcode usa la <strong>Imagen destacada</strong> como imagen principal del evento.</span></div>
            </div>
        </div>
        <?php
    }

    // ─── Panel: Reconocimientos ─────────────────────────────────────────────────

    private function render_panel_reconocimientos(): void {
        ?>
        <div id="mc-help-panel-reconocimientos" class="mc-help-panel">
            <div class="mc-help-card">
                <h2>Reconocimientos <code>mc_reconocimiento</code></h2>
                <p>Reconocimientos corporativos a colaboradores del grupo. Se muestran en una grilla usando el shortcode <code>[mc_reconocimientos]</code>.</p>

                <h3>Cómo agregar o editar un Reconocimiento</h3>
                <ol class="mc-help-steps">
                    <li>Ve a <strong>Reconocimientos → Agregar Reconocimiento</strong>.</li>
                    <li>En el <strong>Título</strong> escribe el nombre de la persona reconocida.</li>
                    <li>En el editor principal escribe la descripción del reconocimiento (logro, motivo, etc.).</li>
                    <li>Sube la <strong>Imagen destacada</strong> con la foto de la persona.</li>
                    <li>Asigna la taxonomía <strong>Empresa</strong> si aplica.</li>
                    <li>Publica el reconocimiento.</li>
                </ol>

                <h3>Campos usados</h3>
                <table class="mc-help-fields">
                    <thead><tr><th>Campo</th><th>Uso en el shortcode</th></tr></thead>
                    <tbody>
                        <tr><td>Título</td><td>Nombre de la persona reconocida</td></tr>
                        <tr><td>Contenido (editor)</td><td>Descripción del reconocimiento</td></tr>
                        <tr><td>Imagen destacada</td><td>Foto de la persona (se muestra como avatar)</td></tr>
                    </tbody>
                </table>

                <div class="mc-help-tip"><span class="dashicons dashicons-info"></span><span>Los reconocimientos se muestran del más reciente al más antiguo según la fecha de publicación.</span></div>
            </div>
        </div>
        <?php
    }

    // ─── Panel: Sedes ───────────────────────────────────────────────────────────

    private function render_panel_sedes(): void {
        ?>
        <div id="mc-help-panel-sedes" class="mc-help-panel">
            <div class="mc-help-card">
                <h2>Sedes <code>mc_sede</code></h2>
                <p>Ubicaciones físicas de las empresas que se muestran en el <strong>footer</strong> del sitio. Cada sede es una tarjeta con nombre, dirección y enlace a Google Maps.</p>

                <h3>Cómo agregar o editar una Sede</h3>
                <ol class="mc-help-steps">
                    <li>Ve a <strong>Sedes → Agregar Sede</strong>.</li>
                    <li>En el <strong>Título</strong> escribe el nombre de la sede (ej. "Sede Principal Medellín").</li>
                    <li>Completa los campos del bloque <strong>"Datos de la Sede"</strong>.</li>
                    <li>Usa <strong>Orden del menú</strong> en el panel lateral para controlar el orden de aparición (número menor = primero).</li>
                    <li>Publica la sede.</li>
                </ol>

                <h3>Campos del bloque "Datos de la Sede"</h3>
                <table class="mc-help-fields">
                    <thead><tr><th>Campo interno</th><th>Etiqueta</th><th>Descripción</th></tr></thead>
                    <tbody>
                        <tr><td>company_label</td><td>Company Label</td><td>Nombre de la empresa al que pertenece esta sede (ej. "Projection Anstra"). Se muestra como subtítulo.</td></tr>
                        <tr><td>address_full</td><td>Address</td><td>Dirección completa de la sede. Puede ser multilínea.</td></tr>
                        <tr><td>maps_url</td><td>Maps URL</td><td>Enlace de Google Maps a la ubicación. Si se deja vacío no aparece el botón "Ver mapa".</td></tr>
                        <tr><td>sede_font_color</td><td>Color de fuente</td><td>Color personalizado para el texto (nombre y dirección). Si no se activa, usa los colores del tema.</td></tr>
                        <tr><td>sede_logo_id</td><td>Logo de Sede</td><td>Logo específico de esta sede. Si está vacío, se usa el logo general de la empresa.</td></tr>
                    </tbody>
                </table>

                <h3>Cómo agregar el logo de la sede</h3>
                <ol class="mc-help-steps">
                    <li>En el bloque "Datos de la Sede", haz clic en <strong>Seleccionar logo</strong>.</li>
                    <li>Elige la imagen de la biblioteca de medios y haz clic en <strong>Usar logo</strong>.</li>
                    <li>Para quitar el logo usa el botón <strong>Quitar logo</strong>.</li>
                </ol>

                <div class="mc-help-tip"><span class="dashicons dashicons-info"></span><span>El color de fuente personalizado es útil cuando la tarjeta tiene fondo oscuro (por el branding de la empresa) y el texto blanco/claro es necesario para legibilidad.</span></div>
            </div>
        </div>
        <?php
    }

    // ─── Panel: Directorio ──────────────────────────────────────────────────────

    private function render_panel_directorio(): void {
        ?>
        <div id="mc-help-panel-directorio" class="mc-help-panel">
            <div class="mc-help-card">
                <h2>Directorio de Contactos <code>mc_directorio</code></h2>
                <p>Directorio interno de colaboradores agrupados por área. Se muestra con pestañas usando el shortcode <code>[mc_directorio_contactos]</code>.</p>

                <h3>Cómo agregar o editar un Contacto</h3>
                <ol class="mc-help-steps">
                    <li>Ve a <strong>Directorio Contactos → Agregar Contacto</strong>.</li>
                    <li>El <strong>Título</strong> del post puede ser cualquier identificador interno (el nombre real se guarda en el campo "Nombre").</li>
                    <li>Completa todos los campos del bloque <strong>"Datos del Contacto"</strong>.</li>
                    <li>Publica el contacto.</li>
                </ol>

                <h3>Campos del bloque "Datos del Contacto"</h3>
                <table class="mc-help-fields">
                    <thead><tr><th>Campo interno</th><th>Etiqueta</th><th>Descripción</th></tr></thead>
                    <tbody>
                        <tr><td>company_context</td><td>Empresa</td><td>Empresa a la que pertenece. Seleccionar de la lista: <code>mc</code>, <code>anstra</code>, <code>essenza</code>, <code>budefry</code>, <code>interactua</code>.</td></tr>
                        <tr><td>area</td><td>Área</td><td>Área o departamento del contacto (ej. "Recursos Humanos"). Se usa para agrupar con pestañas en el directorio.</td></tr>
                        <tr><td>cargo</td><td>Cargo</td><td>Cargo o posición del colaborador.</td></tr>
                        <tr><td>nombre</td><td>Nombre</td><td>Nombre completo del colaborador (este es el nombre visible en la tarjeta).</td></tr>
                        <tr><td>celular</td><td>Celular</td><td>Número de celular o extensión. Se convierte en enlace <code>tel:</code>.</td></tr>
                        <tr><td>email</td><td>Email</td><td>Correo electrónico corporativo. Se convierte en enlace <code>mailto:</code>.</td></tr>
                    </tbody>
                </table>

                <h3>Importación masiva de contactos</h3>
                <p>Existe un importador CSV. Ve a <strong>Directorio Contactos → Importar CSV</strong> (si el menú está disponible). El CSV debe tener columnas: <code>nombre</code>, <code>cargo</code>, <code>area</code>, <code>email</code>, <code>celular</code>, <code>empresa</code>.</p>

                <div class="mc-help-tip"><span class="dashicons dashicons-info"></span><span>El directorio agrupa automáticamente los contactos por área (campo "Área"). Si dos contactos tienen áreas distintas, cada una tendrá su pestaña en el shortcode.</span></div>
            </div>
        </div>
        <?php
    }

    // ─── Panel: Portales ────────────────────────────────────────────────────────

    private function render_panel_portales(): void {
        ?>
        <div id="mc-help-panel-portales" class="mc-help-panel">
            <div class="mc-help-card">
                <h2>Portales de Empresa <code>mc_company_portal</code></h2>
                <p>Tarjetas del portal principal que enlazan a las páginas de cada empresa. Se muestran con el shortcode <code>[mc_company_portals]</code> en la página de inicio.</p>

                <h3>Cómo agregar o editar un Portal</h3>
                <ol class="mc-help-steps">
                    <li>Ve a <strong>Portales de Empresa → Agregar Portal de Empresa</strong>.</li>
                    <li>Escribe un <strong>Título</strong> descriptivo (referencia interna).</li>
                    <li>Completa los campos del bloque <strong>"Datos del Portal de Empresa"</strong>.</li>
                    <li>Usa el <strong>Orden del menú</strong> para controlar el orden de aparición.</li>
                    <li>Publica el portal.</li>
                </ol>

                <h3>Campos del bloque "Datos del Portal de Empresa"</h3>
                <table class="mc-help-fields">
                    <thead><tr><th>Campo interno</th><th>Etiqueta</th><th>Descripción</th></tr></thead>
                    <tbody>
                        <tr><td>portal_slug</td><td>Slug</td><td>Identificador único de la empresa: <code>anstra</code>, <code>essenza</code>, <code>budefry</code>, <code>interactua</code>. Debe coincidir con la URL de la página.</td></tr>
                        <tr><td>portal_name</td><td>Nombre</td><td>Nombre visible en la tarjeta (ej. "Projection Anstra"). Si se deja vacío se usa el Título del post.</td></tr>
                        <tr><td>portal_desc</td><td>Descripción</td><td>Texto de descripción corta que aparece en la tarjeta.</td></tr>
                        <tr><td>portal_color_start</td><td>Color Start</td><td>Color inicial del gradiente de la tarjeta (hex, ej. <code>#1A2E52</code>).</td></tr>
                        <tr><td>portal_color_end</td><td>Color End</td><td>Color final del gradiente de la tarjeta.</td></tr>
                        <tr><td>portal_link_color</td><td>Link Color</td><td>Color del enlace "Ver portal" en la tarjeta.</td></tr>
                        <tr><td>portal_header_bg_color</td><td>Header BG Color</td><td>Color de fondo del encabezado en la página de la empresa.</td></tr>
                        <tr><td>portal_header_text_color</td><td>Header Text Color</td><td>Color del texto en el encabezado de la página de la empresa.</td></tr>
                        <tr><td>portal_url</td><td>URL</td><td>URL de destino de la tarjeta. Si se deja vacío se genera automáticamente como <code>/[slug]/</code>.</td></tr>
                        <tr><td>portal_tags</td><td>Tags</td><td>Etiquetas separadas por coma que aparecen en la tarjeta (ej. <code>RRHH, Contabilidad, Administración</code>).</td></tr>
                        <tr><td>portal_count_label</td><td>Count Label</td><td>Texto del contador en la tarjeta (ej. <code>4 formularios</code>, <code>Novedades</code>).</td></tr>
                    </tbody>
                </table>

                <div class="mc-help-tip"><span class="dashicons dashicons-info"></span><span>Si no hay portales publicados, el shortcode usa los portales por defecto definidos en el plugin (Anstra, Essenza, Budefry, Interactúa).</span></div>
                <div class="mc-help-warning"><span class="dashicons dashicons-warning"></span><span>El <strong>Slug</strong> debe coincidir exactamente con el slug de la página de WordPress para que la URL generada automáticamente funcione.</span></div>
            </div>
        </div>
        <?php
    }

    // ─── Panel: Shortcodes ──────────────────────────────────────────────────────

    private function render_panel_shortcodes(): void {
        ?>
        <div id="mc-help-panel-shortcodes" class="mc-help-panel">
            <div class="mc-help-card">
                <h2>Referencia de Shortcodes</h2>
                <p>Todos los shortcodes disponibles en la intranet. Se insertan en el editor de páginas de WordPress usando corchetes.</p>
            </div>

            <div class="mc-help-card">
                <h3><code>[mc_formularios]</code> — Grilla de formularios</h3>
                <p>Muestra una grilla de tarjetas con los formularios de una empresa y área.</p>
                <div class="mc-help-shortcode">[mc_formularios empresa="anstra" area="rrhh"]</div>
                <table class="mc-help-fields">
                    <thead><tr><th>Atributo</th><th>Default</th><th>Descripción</th></tr></thead>
                    <tbody>
                        <tr><td>empresa</td><td>mc</td><td>Slug de empresa: <code>mc</code> | <code>anstra</code> | <code>essenza</code> | <code>budefry</code> | <code>interactua</code></td></tr>
                        <tr><td>area</td><td>(vacío = todos)</td><td>Slug de área: <code>administracion</code> | <code>tic</code> | <code>gestiones</code> | <code>rrhh</code> | <code>cultura</code></td></tr>
                        <tr><td>featured</td><td>(vacío)</td><td>Usar <code>featured="yes"</code> para mostrar solo los destacados</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="mc-help-card">
                <h3><code>[mc_company_portals]</code> — Portales corporativos</h3>
                <p>Muestra la grilla de portales de empresa en la página de inicio. No requiere atributos.</p>
                <div class="mc-help-shortcode">[mc_company_portals]</div>
            </div>

            <div class="mc-help-card">
                <h3><code>[mc_sedes]</code> — Sedes en el footer</h3>
                <p>Muestra las tarjetas de sedes. Opcionalmente filtradas por empresa.</p>
                <div class="mc-help-shortcode">[mc_sedes empresa="anstra"]</div>
                <table class="mc-help-fields">
                    <thead><tr><th>Atributo</th><th>Default</th><th>Descripción</th></tr></thead>
                    <tbody>
                        <tr><td>empresa</td><td>(vacío = todas)</td><td>Filtrar por slug de empresa. Si se omite muestra todas las sedes publicadas.</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="mc-help-card">
                <h3><code>[mc_reconocimientos]</code> — Grilla de reconocimientos</h3>
                <p>Muestra los reconocimientos más recientes.</p>
                <div class="mc-help-shortcode">[mc_reconocimientos limit="10"]</div>
                <table class="mc-help-fields">
                    <thead><tr><th>Atributo</th><th>Default</th><th>Descripción</th></tr></thead>
                    <tbody>
                        <tr><td>limit</td><td>10</td><td>Número máximo de reconocimientos a mostrar</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="mc-help-card">
                <h3><code>[mc_eventos]</code> — Línea de tiempo de eventos</h3>
                <p>Muestra los eventos en orden de fecha.</p>
                <div class="mc-help-shortcode">[mc_eventos limit="10" upcoming="yes"]</div>
                <table class="mc-help-fields">
                    <thead><tr><th>Atributo</th><th>Default</th><th>Descripción</th></tr></thead>
                    <tbody>
                        <tr><td>limit</td><td>10</td><td>Número máximo de eventos</td></tr>
                        <tr><td>upcoming</td><td>(vacío)</td><td>Usar <code>upcoming="yes"</code> para mostrar solo eventos futuros en orden ascendente</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="mc-help-card">
                <h3><code>[mc_directorio_contactos]</code> — Directorio con pestañas</h3>
                <p>Muestra el directorio de contactos agrupado por área, con pestañas y buscador.</p>
                <div class="mc-help-shortcode">[mc_directorio_contactos empresa="anstra"]</div>
                <table class="mc-help-fields">
                    <thead><tr><th>Atributo</th><th>Default</th><th>Descripción</th></tr></thead>
                    <tbody>
                        <tr><td>empresa</td><td>(vacío = todos)</td><td>Filtrar por slug de empresa</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="mc-help-card">
                <h3><code>[mc_context_alert]</code> — Alerta de contexto de empresa</h3>
                <p>Muestra un aviso informativo en la parte superior de los portales de empresa.</p>
                <div class="mc-help-shortcode">[mc_context_alert empresa="anstra"]</div>
                <table class="mc-help-fields">
                    <thead><tr><th>Atributo</th><th>Default</th><th>Descripción</th></tr></thead>
                    <tbody>
                        <tr><td>empresa</td><td>(auto desde contexto)</td><td>Slug de empresa. Si se omite se detecta automáticamente según la página.</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="mc-help-card">
                <h3><code>[display-posts]</code> — Lista de boletines/posts</h3>
                <p>Muestra los posts del blog (boletines o noticias) con buscador y filtro por categoría.</p>
                <div class="mc-help-shortcode">[display-posts subtitle="Boletines" posts_per_page="40"]</div>
                <table class="mc-help-fields">
                    <thead><tr><th>Atributo</th><th>Default</th><th>Descripción</th></tr></thead>
                    <tbody>
                        <tr><td>subtitle</td><td>Boletines</td><td>Título de la sección</td></tr>
                        <tr><td>posts_per_page</td><td>40</td><td>Número de posts a mostrar</td></tr>
                        <tr><td>empresa</td><td>interactua</td><td>Contexto de empresa (reservado para filtrado futuro)</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="mc-help-card">
                <h3><code>[mc_login_screen]</code> — Pantalla de login</h3>
                <p>Muestra el formulario de autenticación. Se usa en la página de login del sitio. No requiere atributos en uso normal.</p>
                <div class="mc-help-shortcode">[mc_login_screen]</div>
            </div>
        </div>
        <?php
    }

    // ─── Panel: Branding ────────────────────────────────────────────────────────

    private function render_panel_branding(): void {
        $branding_url = admin_url( 'options-general.php?page=mc-intranet-branding' );
        ?>
        <div id="mc-help-panel-branding" class="mc-help-panel">
            <div class="mc-help-card">
                <h2>Branding de Empresas</h2>
                <p>Desde <a href="<?php echo esc_url( $branding_url ); ?>" target="_blank"><strong>Ajustes → Branding Empresas</strong></a> se configuran los colores, logos y textos del Hero para cada empresa del grupo.</p>

                <h3>Cómo acceder</h3>
                <ol class="mc-help-steps">
                    <li>Ve a <strong>Ajustes → Branding Empresas</strong> en el menú lateral.</li>
                    <li>Verás una sección por cada empresa: Anstra, Essenza, Budefry e Interactúa.</li>
                    <li>Edita los campos que necesites.</li>
                    <li>Haz clic en <strong>Guardar cambios</strong> al final de la página.</li>
                </ol>

                <h3>Campos de branding por empresa</h3>
                <table class="mc-help-fields">
                    <thead><tr><th>Campo</th><th>Descripción</th></tr></thead>
                    <tbody>
                        <tr><td>Nombre del encabezado</td><td>Nombre de la empresa que aparece en la barra de navegación cuando se navega al portal de esa empresa</td></tr>
                        <tr><td>Hero: texto superior (eyebrow)</td><td>Texto pequeño sobre el título en la sección Hero (ej. "Portal Empresarial · NIT 901 967 530-0")</td></tr>
                        <tr><td>Hero: título línea 1</td><td>Primera línea del título grande del Hero</td></tr>
                        <tr><td>Hero: título línea 2</td><td>Segunda línea del título (opcional, puede dejarse vacío)</td></tr>
                        <tr><td>Hero: descripción</td><td>Párrafo descriptivo del portal de la empresa</td></tr>
                        <tr><td>Hero: color de fondo</td><td>Color de fondo de la sección Hero (se muestra detrás del texto y el logo)</td></tr>
                        <tr><td>Color de fondo del encabezado</td><td>Color del header/navbar cuando se está en el portal de esa empresa</td></tr>
                        <tr><td>Color del texto del encabezado</td><td>Color del texto en el header/navbar</td></tr>
                        <tr><td>Logo</td><td>Logo de la empresa para el header (versión oscura, para fondos claros)</td></tr>
                        <tr><td>Logo del Hero (blanco)</td><td>Logo de la empresa para el Hero (versión clara/blanca, para fondos oscuros)</td></tr>
                    </tbody>
                </table>

                <div class="mc-help-tip"><span class="dashicons dashicons-info"></span><span>Los cambios de branding se aplican inmediatamente a todas las páginas del portal de esa empresa.</span></div>
                <div class="mc-help-warning"><span class="dashicons dashicons-warning"></span><span>Los logos deben subirse previamente a la <strong>Biblioteca de Medios</strong> de WordPress antes de seleccionarlos aquí.</span></div>
            </div>
        </div>
        <?php
    }

    // ─── Panel: Páginas del sitio ────────────────────────────────────────────────

    private function render_panel_paginas(): void {
        $pages_url = admin_url( 'edit.php?post_type=page' );
        ?>
        <div id="mc-help-panel-paginas" class="mc-help-panel">
            <div class="mc-help-card">
                <h2>Páginas del sitio</h2>
                <p>Las páginas del sitio se editan desde <a href="<?php echo esc_url( $pages_url ); ?>" target="_blank"><strong>Páginas</strong></a> en el menú lateral. La mayor parte del contenido dinámico se muestra mediante shortcodes — no es necesario modificar el código.</p>

                <div class="mc-help-tip"><span class="dashicons dashicons-info"></span><span>Para editar una página, haz clic en su nombre en la lista de Páginas y modifica el contenido en el editor. El shortcode que ya está insertado seguirá mostrando el contenido actualizado del CPT.</span></div>
            </div>

            <div class="mc-help-card">
                <h2>Página de inicio (Portal principal)</h2>
                <p>Muestra la bienvenida y la grilla de portales de empresa. Contiene:</p>
                <ul>
                    <li><strong>Sección Hero</strong>: editable directamente en el editor de la página.</li>
                    <li><strong>Grilla de portales</strong>: generada por <code>[mc_company_portals]</code>. Para agregar/quitar portales ve a <strong>Portales de Empresa</strong>.</li>
                </ul>
                <h3>Qué se puede editar aquí</h3>
                <ul>
                    <li>Título y párrafo de bienvenida (texto libre en el editor)</li>
                    <li>Para cambiar los portales que aparecen → ve al CPT <a href="javascript:;" onclick="document.querySelector('[data-tab=portales]').click()">Portales de Empresa</a></li>
                </ul>
            </div>

            <div class="mc-help-card">
                <h2>Portales de empresa (Anstra, Essenza, Budefry, Interactúa)</h2>
                <p>Cada empresa tiene su propia página que sigue la misma estructura. Para editar el contenido de cada portal:</p>
                <table class="mc-help-fields">
                    <thead><tr><th>Sección en la página</th><th>Cómo modificarla</th></tr></thead>
                    <tbody>
                        <tr><td>Hero (colores, logo, título, descripción)</td><td><strong>Ajustes → Branding Empresas</strong></td></tr>
                        <tr><td>Alerta de contexto</td><td>Se muestra automáticamente con <code>[mc_context_alert]</code>. El texto está definido en el plugin.</td></tr>
                        <tr><td>Grilla de formularios</td><td>Agrega/edita entradas en <strong>Formularios</strong> con la empresa y área correcta</td></tr>
                        <tr><td>Directorio de contactos</td><td>Agrega/edita entradas en <strong>Directorio Contactos</strong> con la empresa correcta</td></tr>
                        <tr><td>Eventos del portal</td><td>Agrega/edita entradas en <strong>Eventos</strong></td></tr>
                        <tr><td>Reconocimientos</td><td>Agrega/edita entradas en <strong>Reconocimientos</strong></td></tr>
                    </tbody>
                </table>
            </div>

            <div class="mc-help-card">
                <h2>Portal Interactúa</h2>
                <p>Página de cultura corporativa. Además de los shortcodes comunes incluye:</p>
                <table class="mc-help-fields">
                    <thead><tr><th>Sección</th><th>Cómo modificarla</th></tr></thead>
                    <tbody>
                        <tr><td>Boletines / Noticias</td><td>Publicar o editar <strong>Entradas</strong> (Posts) de WordPress. El shortcode <code>[display-posts]</code> los muestra automáticamente.</td></tr>
                        <tr><td>Línea de tiempo de eventos</td><td>Gestionar desde <strong>Eventos</strong> asignando la empresa <code>interactua</code></td></tr>
                        <tr><td>Reconocimientos</td><td>Gestionar desde <strong>Reconocimientos</strong></td></tr>
                    </tbody>
                </table>
                <h3>Cómo publicar un boletín o noticia</h3>
                <ol class="mc-help-steps">
                    <li>Ve a <strong>Entradas → Agregar nueva</strong>.</li>
                    <li>Escribe el título y el contenido del boletín.</li>
                    <li>Sube una <strong>Imagen destacada</strong> (aparecerá como miniatura en la lista).</li>
                    <li>Asigna una o varias <strong>Categorías</strong> (se usarán como filtros en el portal).</li>
                    <li>Publica la entrada.</li>
                </ol>
            </div>

            <div class="mc-help-card">
                <h2>Footer del sitio</h2>
                <p>El footer muestra las sedes de las empresas. Para modificarlo:</p>
                <ul>
                    <li><strong>Agregar/editar sedes</strong>: gestionar desde el CPT <a href="javascript:;" onclick="document.querySelector('[data-tab=sedes]').click()">Sedes</a>.</li>
                    <li><strong>Texto legal, redes sociales o datos de contacto del footer</strong>: editar directamente la plantilla del tema o usar el editor de widgets desde <strong>Apariencia → Widgets</strong>.</li>
                </ul>
            </div>

            <div class="mc-help-card">
                <h2>Página de Login</h2>
                <p>La página de ingreso a la intranet. Contiene el shortcode <code>[mc_login_screen]</code>. Normalmente no requiere edición, pero se pueden personalizar el título y subtítulo:</p>
                <div class="mc-help-shortcode">[mc_login_screen title="Ingresa a la intranet" subtitle="Autentícate con tu usuario corporativo."]</div>
                <div class="mc-help-warning"><span class="dashicons dashicons-warning"></span><span>No elimines el shortcode <code>[mc_login_screen]</code> de esta página — es el que muestra el formulario de autenticación.</span></div>
            </div>
        </div>
        <?php
    }
}
