# Guia de Implementacion: Tema WordPress para MC Intranet (Compatible con Elementor)

## 1. Objetivo
Este documento define la forma recomendada de construir y mantener el tema WordPress de la intranet corporativa MC, alineado con:

- Analisis funcional multicompañia (Inicio, empresas, Interactua, footer con sedes).
- Arquitectura de informacion definida en los documentos del proyecto.
- Wireframes y sistema de diseno (tokens, componentes, UX, accesibilidad).
- Compatibilidad plena con Elementor para edicion por equipo no tecnico.

## 2. Alcance funcional de la intranet
La intranet es un portal de enrutamiento hacia recursos de Google Workspace:

1. Secciones transversales:
- Administracion
- TIC
- Gestiones

2. Secciones por empresa:
- Projection Anstra
- Essenza Foods
- Budefry

3. Seccion de cultura:
- Interactua (reconocimientos y eventos)

4. Footer global:
- Sedes y enlaces a Google Maps

Regla UX principal: cualquier formulario debe alcanzarse en maximo 2 clics desde Inicio.

## 2.1 Politica operativa: Inicio 100% Elementor
La portada (Inicio) se administra exclusivamente desde Elementor.

1. El archivo `front-page.php` debe actuar como wrapper minimo (header, footer y `the_content()`).
2. El contenido visual de Inicio (hero, secciones, orden de bloques) se edita en Elementor, no en PHP.
3. Los bloques funcionales del negocio se insertan con shortcode en Elementor, por ejemplo:
- `[mc_company_portals]`
- `[mc_formularios empresa="mc" area="administracion"]`
- `[mc_formularios empresa="mc" area="tic"]`
- `[mc_formularios empresa="mc" area="gestiones"]`
4. Para evitar doble fuente de verdad, no volver a hardcodear contenido de Home en plantilla.

## 3. Hallazgos importantes del estado actual
Basado en la revision de archivos del tema y wireframes:

1. El sistema visual ya existe y es utilizable:
- Tokens de color/espaciado/tipografia
- Componentes UI reutilizables
- Variantes por empresa con `data-company`

2. Hay deuda tecnica que debe corregirse antes de produccion:
- Carga duplicada de CSS (en `header.php`, `style.css` con `@import`, y `functions.php` con enqueues).
- Estructura de `page.php` llama `get_sidebar()` sin un `sidebar.php` claro para esta solucion.
- Plantillas y template parts aun no reflejan totalmente la semantica de wireframes.
- Varios enlaces de RRHH estan en `#` (pendiente conectar URLs reales).

3. Arquitectura de contenido para WordPress todavia no esta formalizada (tipos, campos, taxonomias, permisos).

## 4. Enfoque recomendado de arquitectura WordPress + Elementor
Para este caso (intranet administrativa con contenido muy estructurado), se recomienda:

1. Tema propio ligero (base de presentacion y hooks).
2. Un plugin propio de negocio para centralizar CPT, taxonomias, shortcodes, settings y logica reutilizable.
3. Elementor para composicion visual de paginas y bloques, no para modelar negocio.
4. Campos personalizados para datos dinamicos (formularios, badges, empresa, NIT, sedes).
5. Control estricto de roles/capacidades por area.
6. Evitar dependencia de 20+ plugins de widgets; priorizar stack pequeno y mantenible.

### 4.1 Decision principal de arquitectura
La mejor arquitectura para esta intranet no es poner toda la logica dentro del tema ni depender de varios plugins genericos. La recomendacion mas solida es separar responsabilidades asi:

1. Tema `mc-intranet-theme`
- Presentacion, estilos, templates, compatibilidad con Elementor y layout.

2. Plugin `mc-intranet-core`
- CPT, taxonomias, campos, opciones, shortcodes, hooks propios, integraciones futuras y reglas de negocio.

3. Elementor
- Composicion visual, Theme Builder, plantillas globales y contenido dinamico.

Con esta separacion, si el tema cambia en el futuro, el contenido estructurado y la logica corporativa siguen funcionando.

## 5. Plugins y complementos recomendados

### 5.1 Imprescindibles (MVP)
1. Elementor (free o Pro)
- Constructor visual principal.
- Permite administrar layouts sin tocar PHP.

2. Advanced Custom Fields PRO (ACF)
- Define campos estructurados para formularios, tipo de enlace, empresa, NIT, etc.
- Permite contenido dinamico dentro de Elementor.

3. Plugin propio `mc-intranet-core`
- Debe registrar CPT, taxonomias, shortcodes, opciones globales y utilidades de negocio.
- Reemplaza la necesidad de plugins como Custom Post Type UI en un entorno corporativo serio.
- Reduce deuda tecnica y deja el modelo de datos versionado en codigo.

4. Members (MemberPress) o User Role Editor
- Gestion de roles para editores por empresa/area.

5. WP Activity Log
- Auditoria de cambios (ideal en entorno intranet).

6. UpdraftPlus (o BlogVault equivalente)
- Backups programados y restauracion rapida.

7. LiteSpeed Cache (si servidor LiteSpeed) o WP Rocket
- Cache, minificacion y optimizacion de carga.

### 5.2 Recomendados para intranet empresarial
1. Redirection
- Gestion de redirecciones y 404 sin tocar .htaccess.

2. Site Kit by Google (opcional)
- Solo si se requiere medicion interna y performance desde panel.

3. Better Search Replace
- Migraciones entre ambientes.

4. ACF Extended (opcional)
- Solo si el equipo necesita mejorar UX de ACF sin meter constructores adicionales.

### 5.3 Seguridad y acceso interno
1. Wordfence o Sucuri
- Hardening y monitoreo.

2. WPS Hide Login
- Cambia URL de acceso a wp-admin.

3. Plugin de SSO (opcional segun TI)
- miniOrange SAML SSO / OpenID Connect Generic / Azure AD SSO.
- Recomendado si la organizacion usa Google Workspace o Microsoft 365 para identidad centralizada.

### 5.4 Elementor addons (usar con criterio)
Usar solo si faltan widgets criticos:

1. Elementor Pro
- Preferible antes que varios addons si se requiere Theme Builder, contenido dinamico y templates condicionales.

2. Dynamic.ooo o JetEngine (solo para dinamismo avanzado que ACF + plugin propio no cubran).

Nota: cada addon aumenta superficie de fallo y carga. Activar solo lo necesario.

### 5.5 Plugins a evitar salvo necesidad real
1. Plugins de CPT por UI si ya existe `mc-intranet-core`.
2. Packs masivos de widgets Elementor con 40-80 modulos.
3. Plugins de snippets para logica de negocio permanente.
4. Plugins que duplican funciones de ACF o del core plugin.

## 6. Modelo de contenido recomendado

### 6.1 Tipos de contenido
1. Pagina (nativa WP)
- Inicio, Projection Anstra, Essenza Foods, Budefry, Interactua.

2. CPT `mc_formulario`
- Cada item de formulario/documento del portal.

3. CPT `mc_evento`
- Eventos de Interactua.

4. CPT `mc_reconocimiento`
- Reconocimientos corporativos.

5. CPT `mc_sede`
- Sedes para footer (nombre, direccion, link de maps, empresa).

### 6.2 Taxonomias
1. `empresa`
- mc, anstra, essenza, budefry, interactua.

2. `area`
- administracion, tic, gestiones, rrhh, cultura.

### 6.3 Campos ACF sugeridos
En `mc_formulario`:
- `company_context` (select: mc/anstra/essenza/budefry/interactua)
- `area_context` (select)
- `form_type` (select: form/doc/integrated)
- `form_url` (url)
- `is_featured` (true/false)
- `cta_label` (text)
- `open_new_tab` (true/false)
- `order_weight` (number)

En `mc_sede`:
- `maps_url` (url)
- `address_full` (textarea)
- `company_label` (text)

En `mc_evento`:
- `event_date` (date)
- `event_mode` (select: presencial/virtual/hibrido)
- `event_location` (text)
- `event_featured` (true/false)

En `mc_reconocimiento`:
- `person_role` (text)
- `person_company` (select)
- `achievement_type` (select)
- `achievement_excerpt` (textarea)
- `person_initials` (text)

### 6.4 Opciones globales (Settings API o ACF Options Page)
No todo debe ser CPT. La siguiente informacion conviene manejarla como opciones globales:

1. Nombre oficial de la intranet.
2. URLs globales de soporte.
3. Direcciones y mapas del footer.
4. Labels de empresas.
5. Configuracion de CTA globales.
6. Parametros de IA o integraciones futuras.

## 7. Plugin propio recomendado: `mc-intranet-core`

### 7.1 Responsabilidades del plugin
El plugin debe contener lo siguiente:

1. Registro de CPT y taxonomias.
2. Registro de opciones globales.
3. Shortcodes corporativos reutilizables.
4. Funciones helper para resolver empresa activa.
5. Render de cards de formularios, sedes, eventos y reconocimientos.
6. Integraciones futuras con REST API o IA.
7. Roles/capacidades personalizados si el negocio lo requiere.

### 7.2 Estructura sugerida del plugin
```text
mc-intranet-core/
├── mc-intranet-core.php
├── uninstall.php
├── includes/
│   ├── class-plugin.php
│   ├── class-activator.php
│   ├── class-deactivator.php
│   ├── class-post-types.php
│   ├── class-taxonomies.php
│   ├── class-shortcodes.php
│   ├── class-settings.php
│   ├── class-assets.php
│   └── class-company-context.php
├── admin/
│   └── class-admin.php
├── public/
│   └── class-public.php
├── templates/
│   ├── form-card.php
│   ├── footer-locations.php
│   ├── recognition-card.php
│   └── event-item.php
└── assets/
	├── css/
	└── js/
```

### 7.3 Por que usar plugin propio y no solo tema
1. El contenido estructurado no debe depender del tema activo.
2. Los shortcodes deben sobrevivir si en algun momento cambia la capa visual.
3. Las reglas de negocio y permisos son responsabilidad de plugin, no de plantilla.
4. Facilita pruebas, mantenimiento y despliegues por version.

### 7.4 Guardrails tecnicos del plugin
1. Bootstrap minimo y registro de hooks desde clases.
2. `register_activation_hook()` y `register_deactivation_hook()` en archivo principal.
3. Sanitizar en input y escapar en output.
4. Nonces para formularios y acciones admin.
5. `current_user_can()` en toda accion sensible.
6. Sin consultas SQL manuales salvo necesidad real; si existen, usar `$wpdb->prepare()`.

## 8. Shortcodes y componentes dinamicos recomendados

### 8.1 Cuándo usar shortcodes
En este proyecto los shortcodes si tienen sentido porque:

1. Elementor puede insertarlos facilmente.
2. Permiten reutilizar listas dinamicas sin duplicar diseno por pagina.
3. Encapsulan consultas y HTML corporativo controlado.

### 8.2 Shortcodes prioritarios
1. `[mc_form_cards area="administracion" company="mc" featured="0"]`
- Renderiza tarjetas de formularios por area y empresa.

2. `[mc_company_portals]`
- Renderiza las tarjetas de acceso a empresas desde Inicio.

3. `[mc_footer_locations]`
- Renderiza sedes globales o por empresa.

4. `[mc_recognitions limit="6"]`
- Renderiza reconocimientos de Interactua.

5. `[mc_events limit="10" upcoming="1"]`
- Renderiza timeline de eventos.

6. `[mc_context_alert company="anstra"]`
- Renderiza la alerta de contexto bajo el hero.

### 8.3 Helpers en vez de HTML hardcodeado
Los shortcodes deben delegar el markup a templates PHP del plugin o del tema, por ejemplo:

1. `templates/form-card.php`
2. `templates/footer-locations.php`
3. `templates/company-portal-card.php`

Esto evita duplicar HTML en Elementor y mantiene coherencia visual.

### 8.4 Alternativa superior a corto plazo
Si el equipo tiene capacidad, la evolucion ideal es:

1. Empezar con shortcodes.
2. Luego migrar los shortcodes mas usados a widgets custom de Elementor o bloques dinamicos.

## 9. Compatibilidad real con Elementor (tema)
El tema debe permitir que Elementor controle ancho, templates y header/footer sin conflicto.

### 9.1 Requisitos en tema
1. En `functions.php` habilitar:
- `title-tag`
- `post-thumbnails`
- `html5`
- `elementor`
- `elementor-pro` (si usan Pro)

2. Registrar menus:
- `primary`
- `footer`

3. Usar `wp_enqueue_scripts` correctamente:
- Sin `@import` en `style.css` para CSS principal.
- Solo enqueues versionados.

4. En `header.php` y `footer.php`:
- Mantener `wp_head()` y `wp_footer()`.
- Evitar hardcode de estilos que choque con Elementor.

5. Crear `page.php` limpio para canvas completo:
- Sin wrappers que rompan ancho de Elementor.
- Eliminar `get_sidebar()` si no se usa sidebar.

6. Si se usa `body` con `data-company`, resolverlo via hook o helper comun.

### 9.2 Plantillas Elementor
Crear estas plantillas globales:
1. Header global (menu y badge de empresa)
2. Footer global (sedes)
3. Single para `mc_formulario` (si se mostraran detalles)
4. Archive para `mc_evento`
5. Archive para `mc_reconocimiento`
6. Archive por empresa/area (opcional)

### 9.3 Datos dinamicos dentro de Elementor
Usar Dynamic Tags para:
- Titulo de formulario
- Descripcion
- URL de CTA
- Badge por tipo
- Empresa actual (paleta)

### 9.4 Regla operativa con Elementor
No construir manualmente en Elementor grids completos de cards si los datos son dinamicos. Para contenido dinamico repetible, usar:

1. Shortcode del plugin propio.
2. Loop Item + ACF si Elementor Pro cubre el caso.
3. Widget custom solo si shortcodes no bastan.

## 10. Estrategia de estilos (tomando wireframes como fuente)

### 10.1 Orden de carga
1. `design-tokens.css`
2. `components.css`
3. `theme-overrides.css` (si se requiere)

### 10.2 Regla de oro
No duplicar estilos entre:
- `style.css`
- `header.php`
- Enqueues de `functions.php`

Centralizar en `assets/css`.

### 10.3 Integracion con Elementor
1. Desactivar colores y fuentes por defecto de Elementor para respetar tokens.
2. Configurar breakpoints iguales a wireframes.
3. Definir variables globales en `:root` y por `data-company`.

### 10.4 Estrategia de iconografia
1. No depender en produccion de CDN publico de iconos si el portal es interno.
2. Preferir SVG inline, sprite local o libreria empaquetada en el tema/plugin.
3. Si se usan iconos en shortcodes, resolverlos desde un helper central.

## 11. Mapeo de wireframes a paginas WordPress

1. `inicio.html` -> Pagina `Inicio`
- Hero global
- Bloques Administracion, TIC, Gestiones
- Tarjetas de acceso por empresa

2. `projection-anstra.html` -> Pagina `Projection Anstra`
- Hero por empresa
- Alerta de contexto
- RRHH cards

3. `essenza-foods.html` -> Pagina `Essenza Foods`
- Mismo patron de Anstra con paleta Essenza

4. `budefry.html` -> Pagina `Budefry`
- Prioridad mobile y carga rapida

5. `interactua.html` -> Pagina `Interactua`
- Grid de reconocimientos
- Timeline de eventos

## 12. Flujo de implementacion (paso a paso)

### Fase 1: Base tecnica
1. Corregir tema base (enqueues, page template, hooks).
2. Crear `mc-intranet-core` con bootstrap limpio.
3. Activar soporte Elementor completo.
4. Versionar CSS/JS con `filemtime` para evitar cache obsoleto en QA.

### Fase 2: Modelo de datos
1. Registrar CPT y taxonomias en plugin propio.
2. Crear field groups ACF.
3. Crear opciones globales.
4. Cargar data inicial de formularios y sedes.

### Fase 3: Construccion visual
1. Crear Header/Footer en Elementor Theme Builder.
2. Construir pagina Inicio.
3. Construir paginas por empresa reutilizando secciones globales.
4. Construir Interactua con CPT dinamicos.
5. Reemplazar grids manuales por shortcodes o loops dinamicos.

### Fase 4: Integraciones
1. Sustituir todos los `#` por URLs reales.
2. Validar aperturas en nueva pestana para recursos externos.
3. Normalizar enlaces de Google Maps.
4. Preparar endpoints o hooks para integraciones IA futuras.

### Fase 5: QA y salida
1. Pruebas funcionales por rol.
2. Pruebas responsive.
3. Accesibilidad AA.
4. Performance.
5. Hardening seguridad.
6. Validacion de activacion/desactivacion del plugin sin fatals.

## 13. Mejores practicas (obligatorias)

### 13.1 Rendimiento
1. No cargar librerias de iconos completas si no es necesario.
2. Usar SVG inline o subset de iconos.
3. Lazy load en imagenes no criticas.
4. Evitar addons innecesarios de Elementor.
5. Objetivo recomendado: LCP < 2.5s en red corporativa.

### 13.2 Accesibilidad
1. Contraste minimo AA.
2. Focus visible en todos los controles.
3. Labels semanticos (`aria-label`, `aria-current`, etc.).
4. Navegacion por teclado completa en menu mobile.

### 13.3 Seguridad
1. Bloquear edicion de archivos desde admin (`DISALLOW_FILE_EDIT`).
2. Limitar intentos de login.
3. Backups diarios.
4. Actualizaciones de plugins con ventana controlada.
5. Toda accion admin propia debe usar nonce + capability check.
6. Toda salida HTML dinamica debe escapar segun contexto (`esc_html`, `esc_attr`, `esc_url`).
7. No meter logica sensible en snippets sueltos del theme.

### 13.4 Gobierno de contenido
1. No permitir que editores cambien estructura de plantillas globales sin flujo de aprobacion.
2. Definir responsables por empresa.
3. Versionar cambios visuales mayores en ramas separadas.

### 13.5 Desarrollo WordPress mantenible
1. Seguir WordPress Coding Standards.
2. Pasar `phpcs --standard=WordPress` al plugin y tema cuando el proyecto incorpore tooling.
3. Mantener strings traducibles con text domain propio.
4. Evitar side effects grandes al cargar archivos PHP.

## 14. Configuracion recomendada de Elementor
1. Active el modo optimizado de carga de assets.
2. Desactive Google Fonts de Elementor si las fuentes ya vienen por tema/tokens.
3. Defina contenedor principal max-width igual a tokens (`--container-max`).
4. Reutilice templates/secciones para no duplicar bloques por empresa.
5. Use condiciones de visualizacion por pagina/empresa para header badge y alertas de contexto.
6. Defina presets globales de tipografia y espaciado a partir de los tokens del sistema.
7. Si usa Loop Grid, alimentar desde CPT y no desde contenido copiado manualmente.

## 15. Checklist de preproduccion

### Funcional
- [ ] Todos los links de formularios reales cargados.
- [ ] No quedan CTAs en `#`.
- [ ] Menus activos y estados `aria-current` correctos.

### Visual
- [ ] Paleta correcta por empresa.
- [ ] Footer muestra sedes y mapas validos.
- [ ] Responsive validado en 360px, 768px, 1024px y 1440px.

### Tecnico
- [ ] Sin duplicidad de CSS/JS.
- [ ] Sin warnings PHP en logs.
- [ ] Sin plugins inactivos residuales.
- [ ] Plugin `mc-intranet-core` activa y desactiva sin errores.
- [ ] Shortcodes renderizan sin notices ni HTML roto.

### Seguridad/Operacion
- [ ] Backups automaticos probados.
- [ ] Roles y permisos revisados.
- [ ] Registro de actividad habilitado.
- [ ] Nonces y capability checks verificados en funciones admin propias.

## 16. Roadmap sugerido post-lanzamiento
1. Fase 1: Portal estable + formularios reales + control de roles.
2. Fase 2: Directorio corporativo dinamico como CPT o integracion externa.
3. Fase 3: Widget Elementor propio o bloques dinamicos para formularios/eventos.
4. Fase 4: Integraciones IA (enrutamiento, FAQ, prellenado asistido).
5. Fase 5: SSO corporativo y analitica de uso por area.

## 17. Resumen ejecutivo
Para garantizar una intranet robusta y mantenible:

1. Construya sobre tema ligero + plugin propio `mc-intranet-core` + Elementor + ACF.
2. Modele datos (formularios/eventos/sedes) en CPT y opciones, no en HTML hardcodeado.
3. Use shortcodes propios para listas dinamicas y deje Elementor para la composicion visual.
4. Mantenga un stack corto de plugins (funcion > moda).
5. Asegure accesibilidad AA, rendimiento, seguridad y auditoria de cambios.
6. Respete el sistema de diseno existente basado en tokens y `data-company`.

Con este enfoque, el equipo podra operar el portal sin dependencia constante de desarrollo, manteniendo consistencia visual y control tecnico en un entorno intranet empresarial.
