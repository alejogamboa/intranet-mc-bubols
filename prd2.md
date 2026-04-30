📘 PRD EJECUTABLE — INTRANET WORDPRESS MULTICOMPAÑÍA

Version: 1.0 (Ejecutable)
Fecha: 2026-04-23
Estado: Ready for AI Implementation

1. DECISIONES TÉCNICAS OBLIGATORIAS
Stack oficial:

- CMS: WordPress (última versión estable)
- Arquitectura: Tema custom + Elementor + ACF + Plugin core propio
- Base de datos: MySQL 8+
- Entorno local: Docker Compose
- Automatización: WP-CLI

Restricciones:

- NO usar Gutenberg para layouts
- NO lógica de negocio en functions.php
- TODO CPT y lógica en plugin core
- Elementor SOLO para composición visual

Plugins permitidos:

- Elementor
- Advanced Custom Fields (ACF)
- Plugin core (custom)
- Plugin de caché (WP Rocket o equivalente)

Plugins NO permitidos:

- Page builders adicionales
- Plugins duplicados de funcionalidad
2. ARQUITECTURA GENERAL
Separación de responsabilidades:

1. Tema (theme/)
   - Layout
   - CSS (design tokens)
   - Componentes visuales
   - Templates Elementor

2. Plugin core (plugin-core/)
   - CPT
   - Taxonomías
   - Shortcodes
   - Lógica de negocio
   - Hooks

3. Elementor
   - Composición de páginas
   - Uso de shortcodes dinámicos
3. MODELO DE DATOS (CONTRATOS)
3.1 CPT: mc_formulario
Post Type: mc_formulario

Campos:

- company_context:
  type: enum
  values: [mc, anstra, essenza, budefry]
  required: true

- area_context:
  type: enum
  values: [administracion, tic, gestiones, rrhh]
  required: true

- form_type:
  type: string
  required: true

- form_url:
  type: string
  required: true
  validation: must_be_valid_url

- cta_label:
  type: string
  default: "Abrir"

- open_new_tab:
  type: boolean
  default: true

- is_featured:
  type: boolean
  default: false

- order_weight:
  type: integer
  range: 0-100
  default: 50
3.2 CPT: mc_evento
Campos:

- title
- description
- event_date (date)
- company_context (interactua)
3.3 CPT: mc_reconocimiento
Campos:

- title
- description
- employee_name
- company_context
3.4 CPT: mc_sede
Campos:

- name
- address
- google_maps_url
3.5 TAXONOMÍAS
empresa:
  [mc, anstra, essenza, budefry, interactua]

area:
  [administracion, tic, gestiones, rrhh, cultura]
4. LÓGICA DE RENDERIZADO (OBLIGATORIA)
4.1 HOME
Query:

SELECT mc_formulario WHERE:
- company_context = mc
- area_context IN [administracion, tic, gestiones]

Orden:
- order_weight ASC

Render:

- Agrupar por area_context
- Cada grupo = sección
- Cada item = card

Card behavior:

- CTA → form_url
- Si open_new_tab = true → target="_blank"

Estados:

- Si form_url es null → mostrar estado "Pendiente"
4.2 PORTALES POR EMPRESA
Para cada empresa:

Query:

SELECT mc_formulario WHERE:
- company_context = {empresa}
- area_context = rrhh

Orden:
- order_weight ASC

Render:

- Lista de cards homogénea
4.3 INTERACTUA
Eventos:

SELECT mc_evento ORDER BY event_date DESC LIMIT 10

Reconocimientos:

SELECT mc_reconocimiento ORDER BY date DESC LIMIT 10
5. SHORTCODES (OBLIGATORIOS)
[mc_formularios empresa="mc" area="tic"]
→ Renderiza cards dinámicos

[mc_eventos]
→ Lista de eventos

[mc_reconocimientos]
→ Lista de reconocimientos

[mc_sedes]
→ Footer con sedes
6. NAVEGACIÓN (REGLAS PROGRAMABLES)
Menú principal:

1. Inicio
2. Projection Anstra
3. Essenza Foods
4. Budefry
5. Interactua

Regla obligatoria:

- Cualquier recurso debe ser accesible en ≤ 2 clics desde Home

Validación automática:

- Si profundidad > 2 → error de arquitectura
7. SISTEMA VISUAL (IMPLEMENTABLE)
Tipografía:

- Sora (headings)
- Inter (body)

Contexto por empresa:

- Usar atributo: data-company="anstra|essenza|budefry"

Componentes obligatorios:

- navbar
- hero
- card
- alert
- footer

Responsive breakpoints:

- mobile: <640px
- tablet: 640–900px
- desktop: >900px
8. ACCESIBILIDAD (VALIDABLE)
Requisitos:

- Contraste WCAG AA
- Navegación por teclado
- Focus visible
- ARIA labels en:
  - navegación
  - botones
  - enlaces externos
9. PERFORMANCE (IMPLEMENTACIÓN)
Objetivo:

- < 3s carga inicial

Estrategia obligatoria:

- Cache plugin activo
- Minificación CSS/JS
- Lazy load imágenes
- Eliminar CSS/JS no usado de Elementor
10. SEGURIDAD (BASELINE)
Hardening:

- Deshabilitar XML-RPC
- Limitar intentos de login
- Forzar HTTPS

Roles:

- admin
- editor
- rrhh
- tic

Restricción:

- Solo admin instala plugins
11. ENTORNO LOCAL (AUTOMATIZABLE)
Docker services:

- wordpress
- mysql
- wp-cli

Comandos obligatorios:

1. levantar entorno
2. instalar wordpress
3. activar tema
4. activar plugin core
5. crear usuario editor
12. DEPLOYMENT (CI/CD)
Entornos:

- local
- staging
- production

Deploy:

- Basado en Git
- Scripts WP-CLI para:
  - migraciones
  - activación plugins
  - importación inicial de datos
13. MATRIZ DE FORMULARIOS (ESTRUCTURADA)
Estados permitidos:

- listo
- pendiente
- validar

Regla:

- NO se permite deploy a producción con estado "pendiente" en formularios críticos
14. CRITERIOS DE ACEPTACIÓN (TESTEABLES)
Funcionales:

- 100% de formularios accesibles en ≤ 2 clics

Técnicos:

- 0 errores PHP
- 0 CSS duplicado crítico

UX:

- No layout breaks en mobile/tablet/desktop
15. RESTRICCIONES FUTURAS (IMPORTANTE)
Google Forms:

- Uso permitido solo en MVP
- Debe ser reemplazable por API interna en Fase 3