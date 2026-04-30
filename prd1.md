# Especificacion Preliminar para Construccion de Intranet en WordPress

Version: 0.1 (Preliminar)
Fecha: 23 de abril de 2026
Estado: En definicion funcional y tecnica

## 1. Proposito del documento

Definir una base de especificacion para implementar la Intranet Corporativa en WordPress, migrando el modelo actual de portal multicompañia y manteniendo la arquitectura de contenidos, la identidad visual de marca y los puntos de interaccion operativa.

Este documento sirve para:

- Alinear negocio, diseno, desarrollo y QA.
- Delimitar el alcance de MVP.
- Definir una hoja de ruta fase 2/3.
- Registrar riesgos, dependencias y pendientes criticos.

## 2. Fuentes de entrada consideradas

- analisis_intranet.md
- arquitectura_intranet.md
- wireframes/brand/brand-manual.html
- wireframes/docs/ux-principles.md
- wireframes/css/design-tokens.css
- wireframes/css/components.css
- wireframes/screens/*.html
- mc-intranet-wordpress/docs/PRD.md
- mc-intranet-wordpress/docs/INSTRUCCIONES_TEMA_WORDPRESS_ELEMENTOR.md
- mc-intranet-wordpress/docs/COMANDOS_WP_CLI.md

## 3. Objetivo del proyecto

Construir una intranet WordPress multicompañia para Projection Anstra, Essenza Foods y Budefry, con una seccion transversal corporativa y una seccion de cultura (Interactua), priorizando:

- Regla UX de maximo 2 clics para llegar a formularios/documentos.
- Claridad de contexto por empresa.
- Operacion simple para equipos no tecnicos.
- Escalabilidad para integraciones futuras (IA, API, automatizaciones).

## 4. Alcance funcional

### 4.1 Incluye (MVP)

1. Inicio multicompañia con secciones transversales:
- Administracion
- TIC
- Gestiones

2. Portales por empresa:
- Projection Anstra
- Essenza Foods
- Budefry

3. Seccion Interactua:
- Reconocimientos
- Eventos

4. Footer global con sedes y enlaces a Google Maps.

5. Integracion con enlaces a Google Forms y Google Docs.

6. Navegacion responsive y accesible en escritorio y movil.

### 4.2 No incluye (MVP)

- Automatizacion IA en produccion.
- Integracion SSO obligatoria (queda como opcion).
- Workflow de aprobacion interno nativo en WordPress.
- Analitica avanzada de uso por area.

## 5. Arquitectura de informacion

La estructura de navegacion principal debe conservar 5 entradas:

1. Inicio
2. Projection Anstra
3. Essenza Foods
4. Budefry
5. Interactua

Regla de navegacion:

- Desde Inicio a cualquier recurso operativo: maximo 2 interacciones.
- Evitar subniveles profundos y paginas intermedias sin CTA.

## 6. Especificacion funcional por seccion

### 6.1 Inicio

Debe concentrar enlaces transversales a:

- Administracion: Tiquetes, Viaticos, Hospedaje.
- TIC: Soporte, Gestion de usuarios, Compras.
- Gestiones: Logistica no venta, Certificados de votacion.

Comportamiento:

- Cada tarjeta abre el recurso en nueva pestana cuando aplique.
- Debe existir estado visual para recursos pendientes de URL.

### 6.2 Portales por empresa

Cada empresa debe mostrar su seccion de RRHH con estructura homologada:

1. Perfil sociodemografico.
2. Certificado laboral.
3. Formato solicitud de mejora.
4. Inicio de proceso disciplinario.
5. Directorio corporativo.

Requisito critico:

- El usuario siempre debe identificar la empresa activa mediante color, badge y alerta de contexto.

### 6.3 Interactua

Debe publicar contenido de cultura interna:

- Reconocimientos corporativos.
- Eventos importantes.

## 7. Sistema visual y experiencia de usuario

Se adopta el sistema de diseno definido en wireframes (tokens + componentes).

### 7.1 Principios UX obligatorios

- Claridad
- Eficiencia
- Confianza

### 7.2 Identidad visual

- Tipografia: Sora (display) + Inter (body/UI).
- Paleta por contexto de empresa via data-company.
- Componentes base: nav global, hero, form cards, company cards, alertas, footer.

### 7.3 Responsive

- Mobile: < 640px
- Tablet: 640px a 900px
- Desktop: > 900px

Lineamientos:

- CTA con area tactil minima de 44px.
- Navegacion hamburguesa en viewports menores a 900px.
- Grid adaptable sin romper jerarquia de contenido.

### 7.4 Accesibilidad

Objetivo: cumplimiento WCAG 2.1 nivel AA.

Minimos:

- Contraste adecuado en texto funcional.
- Navegacion completa por teclado.
- Focus visible.
- Uso correcto de ARIA en nav, CTA y recursos externos.

## 8. Arquitectura tecnica WordPress (alternativas)

### Alternativa A (recomendada por mantenibilidad)

Tema custom + Elementor + ACF + plugin propio de negocio.

Separacion:

- Tema: presentacion, layout, estilos y compatibilidad visual.
- Plugin core: CPT, taxonomias, shortcodes, reglas de negocio y utilidades.
- Elementor: composicion y mantenimiento por equipo no tecnico.

Ventajas:

- Menor deuda tecnica a mediano plazo.
- Menor acoplamiento entre contenido y tema.
- Escalabilidad para integraciones futuras.

### Alternativa B

Tema custom sin Elementor (bloques nativos + plantillas custom).

Ventajas:

- Menos dependencia de plugin builder.
- Mayor control tecnico de salida HTML.

Desventajas:

- Mayor esfuerzo inicial de construccion y mantenimiento editorial.
- Menor autonomia para editores no tecnicos.

### Criterios para decidir alternativa

1. Velocidad de salida a produccion.
2. Capacidad tecnica del equipo interno.
3. Nivel de autonomia que necesita contenidos.
4. Costo de mantenimiento anual.

## 9. Modelo de contenido (propuesto)

### 9.1 Tipos de contenido

- Pagina (core): Inicio, empresas, Interactua.
- CPT mc_formulario.
- CPT mc_evento.
- CPT mc_reconocimiento.
- CPT mc_sede.

### 9.2 Taxonomias

- empresa: mc, anstra, essenza, budefry, interactua.
- area: administracion, tic, gestiones, rrhh, cultura.

### 9.3 Campos clave

En mc_formulario:

- company_context
- area_context
- form_type
- form_url
- is_featured
- cta_label
- open_new_tab
- order_weight

## 10. Requisitos no funcionales

### 10.1 Rendimiento

- Tiempo de carga objetivo: < 3 segundos en condiciones corporativas normales.
- Peso objetivo de pantalla (HTML + CSS sin imagenes externas): <= 150 KB cuando aplique.

### 10.2 Seguridad

- Hardening basico WordPress.
- Roles y capacidades definidos por perfil.
- Backups programados y prueba de restauracion.
- Registro de auditoria de cambios.

### 10.3 Mantenibilidad

- Evitar logica de negocio en snippets sueltos.
- Versionar estructura de contenido en codigo.
- Estandarizar templates y componentes reutilizables.

## 11. Entorno de desarrollo y operacion local

Base actual:

- Docker Compose para WordPress + MySQL + WP-CLI.
- Comandos operativos en mc-intranet-wordpress/docs/COMANDOS_WP_CLI.md.

Operaciones minimas requeridas:

1. Levantar entorno local.
2. Verificar DB y core WordPress por CLI.
3. Activar tema.
4. Crear usuario editor.
5. Ajustar permalinks.

## 12. Plan de implementacion por fases

### Fase 1: MVP funcional

Objetivo:

- Publicar la intranet con estructura completa, componentes base y enlaces operativos.

Entregables:

- Navegacion global funcional.
- Inicio con secciones transversales.
- Portales por empresa.
- Interactua base.
- Footer con sedes.
- QA funcional y responsive inicial.

### Fase 2: Fortalecimiento tecnico y editorial

Objetivo:

- Consolidar modelo de contenido dinamico y operacion editorial.

Entregables:

- Plugin core consolidado.
- Shortcodes/componentes dinamicos.
- Mejora de permisos y auditoria.
- Optimizacion de performance.

### Fase 3: Evolucion y automatizacion

Objetivo:

- Preparar integraciones con servicios corporativos e IA.

Entregables:

- Integraciones API priorizadas.
- Posible SSO.
- Automatizaciones de soporte y enrutamiento.

## 13. Criterios de aceptacion y QA

### 13.1 Funcionales

- Todo formulario/documento del alcance se alcanza en maximo 2 clics desde Inicio.
- No existen enlaces criticos en # al cierre de salida a produccion.
- Contexto visual por empresa es correcto en todas las paginas.

### 13.2 Visuales

- Se respeta tipografia, paleta y componentes definidos.
- No hay quiebres en mobile, tablet y desktop.

### 13.3 Tecnicos

- Sin duplicidad de carga CSS/JS.
- Sin warnings/notices de PHP en flujo normal.
- Accesibilidad base aprobada (AA en puntos criticos).

## 14. Riesgos, supuestos y dependencias

### 14.1 Riesgos principales

1. URLs pendientes de formularios por empresa.
2. Deuda tecnica actual del tema (carga duplicada y estructuras incompletas).
3. Dependencia fuerte de recursos externos (Google Forms/Docs/Maps).

### 14.2 Supuestos

1. Se mantendra la arquitectura de informacion actual.
2. Los responsables de cada area entregaran URLs definitivas.
3. El equipo validara el modelo de permisos por rol antes de salir a produccion.

### 14.3 Dependencias

- Contenido validado por RRHH, TIC y Administracion.
- Definicion final de stack (Alternativa A o B).
- Disponibilidad de entorno local y pipeline de despliegue.

## 15. Matriz preliminar de formularios y estado

Estado:

- Listo: URL validada.
- Pendiente: URL no suministrada.
- Validar: URL existe pero falta comprobacion funcional.

| Seccion | Empresa | Recurso | URL | Estado | Owner sugerido |
|---|---|---|---|---|---|
| Administracion | MC | Solicitud Tiquetes | forms.gle/P5G3SVjKKtoYno5A8 | Listo | Administracion |
| Administracion | MC | Solicitud Viaticos | forms.gle/iwwYKaHEe9Ns8avP7 | Listo | Administracion |
| Administracion | MC | Solicitud Hospedaje | forms.gle/ePwLxsPUGVQNxasM6 | Listo | Administracion |
| TIC | MC | Soporte TIC | forms.gle/Jan3qP5n4zK1CkEK8 | Listo | TIC |
| TIC | MC | Gestion Usuarios TIC | forms.gle/zgyn7vaouK9ttLKp8 | Listo | TIC |
| TIC | MC | Compras TIC | forms.gle/Vg93Fqsb4r7ajzsZ9 | Listo | TIC |
| Gestiones | MC | Logistica No Venta | forms.gle/KkM9QLUoq9raV7UE9 | Listo | Administracion |
| Gestiones | MC | Certificados de Votacion | forms.gle/rJcfzBHREexeLEpx5 | Listo | RRHH |
| RRHH | Anstra | Perfil Sociodemografico | PENDIENTE_URL | Pendiente | RRHH Anstra |
| RRHH | Anstra | Certificado Laboral | PENDIENTE_URL | Pendiente | RRHH Anstra |
| RRHH | Essenza | Perfil Sociodemografico | PENDIENTE_URL | Pendiente | RRHH Essenza |
| RRHH | Essenza | Certificado Laboral | PENDIENTE_URL | Pendiente | RRHH Essenza |
| RRHH | Budefry | Perfil Sociodemografico | PENDIENTE_URL | Pendiente | RRHH Budefry |
| RRHH | Budefry | Certificado Laboral | PENDIENTE_URL | Pendiente | RRHH Budefry |

## 16. Definition of Ready para version final

Este documento podra pasar de preliminar a final cuando:

1. Se confirme la alternativa tecnica oficial.
2. Se complete la matriz de URLs pendientes.
3. Se validen responsables (RACI) por frente funcional.
4. Se cierre checklist de QA de MVP.
5. Se apruebe por stakeholders de negocio y tecnologia.

## 17. Registro metodologico (MCP y skills usados)

Para construir este preliminar se utilizaron capacidades del entorno de desarrollo, incluyendo exploracion asistida y lectura estructurada de documentos, con soporte de skills especializados de WordPress y diseno frontend para asegurar coherencia entre negocio, UX y arquitectura tecnica.

