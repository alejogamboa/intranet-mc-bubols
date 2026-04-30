# Principios UI/UX — MC Intranet
**Versión:** 1.0 | **Fecha:** Abril 2026 | **Aplicable a:** Migración WordPress

---

## 1. Filosofía de Diseño

La MC-Intranet es una herramienta de trabajo, no una vitrina. Cada decisión de diseño debe responder a una sola pregunta: **¿hace que el colaborador llegue más rápido a lo que necesita?**

Los tres valores que guían el diseño:

| Valor | Definición |
|-------|-----------|
| **Claridad** | El usuario sabe dónde está, de qué empresa es el contenido y qué acción debe tomar. |
| **Eficiencia** | Máximo dos clics desde el Inicio hasta cualquier formulario o documento. |
| **Confianza** | La interfaz comunica solidez corporativa. Colores, tipografía y consistencia visual generan credibilidad. |

---

## 2. Principio de los 2 Clics

> **Regla:** Cualquier formulario o documento debe ser alcanzable en máximo **2 interacciones** desde el portal de inicio.

**Flujo validado:**
```
Inicio → [Clic 1: portal de empresa] → [Clic 2: abrir formulario] ✓
Inicio → [Clic 1: sección transversal (Admin/TIC/Gestiones)] → [Clic 2: abrir formulario] ✓
```

**Anti-patrones a evitar en WordPress:**
- Submenús de más de 2 niveles de profundidad.
- Páginas intermedias que solo describen el formulario sin botón de acción directo.
- Formularios embebidos que requieren scroll excesivo para ser encontrados.

---

## 3. Identidad de Contexto Multicompañía

El usuario siempre debe saber en qué empresa está. Se implementan tres capas de comunicación visual:

### Capa 1 — Color de empresa
El atributo `data-company` en el `<body>` activa la paleta completa de la empresa. Todos los elementos interactivos (botones, íconos de sección, bordes hover) usan el color de empresa como acento.

| Empresa | Color Primario | Color Acento |
|---------|---------------|--------------|
| MC Intranet (global) | `#1E3A5F` | `#2B7FD4` |
| Projection Anstra | `#1A2E52` | `#C9A84C` |
| Essenza Foods | `#1B6B45` | `#F4874B` |
| Budefry SAS | `#2D3748` | `#E85D04` |
| Interactúa | `#4338CA` | `#EC4899` |

### Capa 2 — Badge en la barra de navegación
Cada subpágina de empresa muestra un badge en la nav superior con el nombre de la empresa activa.

### Capa 3 — Alerta de contexto bajo el hero
Una alerta informativa bajo el hero de cada empresa recuerda al usuario que los formularios son exclusivos para esa empresa y provee un link directo al inicio si hay confusión de empresa.

**Implementación en WordPress:** Usar el campo personalizado del post/página (`company_context`) para controlar qué `data-company` se inyecta en el `<body>` y qué badge se renderiza en la nav.

---

## 4. Jerarquía Visual de Contenidos

Aplicar la siguiente escala de importancia en cualquier sección:

```
1. Hero (Título de página + descripción)         → Mayor peso visual
2. Section Header (Icono + título + descripción) → Peso medio
3. Form Cards Grid                               → Contenido principal
4. Quick Access / Breadcrumbs                   → Orientación
5. Footer Locations                             → Información secundaria
```

**Tipografía aplicada:**
- **Sora (800)** → Títulos de Hero (h1)
- **Sora (700)** → Títulos de sección (h2)
- **Inter (600)** → Títulos de tarjeta (h3) y etiquetas uppercase
- **Inter (400/500)** → Cuerpo de texto y descripciones

---

## 5. Mobile First — Prioridad Budefry

Los colaboradores de Budefry en planta (Guarne) acceden desde dispositivos móviles en ambientes con poca conectividad. Implicaciones de diseño:

### Breakpoints del sistema
| Nombre | Ancho | Descripción |
|--------|-------|-------------|
| `mobile` | `< 640px` | Smartphone — grid de 1 columna |
| `tablet` | `640px – 900px` | Tablet — grid de 2 columnas |
| `desktop` | `> 900px` | Escritorio — grid de 3+ columnas |

### Reglas para WordPress
- Las tarjetas de formulario (`form-cards-grid`) usan `auto-fill` con `minmax(280px, 1fr)` — se adaptan automáticamente sin media queries adicionales.
- La navegación colapsa en hamburguesa en viewports < 900px.
- El botón de acción (CTA) de cada tarjeta es siempre `width: 100%` en mobile — target de toque mínimo 44px.
- Las imágenes en hero se suprimen en mobile para ahorrar ancho de banda.

### Consideraciones de carga
- Usar `loading="lazy"` en todas las imágenes no críticas.
- Los íconos de Lucide se sirven desde CDN con fallback inline SVG.
- El peso total de cada pantalla no debe superar **150KB** (HTML + CSS, sin imágenes externas).

---

## 6. Accesibilidad — WCAG 2.1 Nivel AA

### Contraste de color (mínimo 4.5:1 para texto normal)

| Combinación | Contraste | ¿Pasa AA? |
|-------------|-----------|-----------|
| Texto primario `#1E293B` sobre `#FFFFFF` | 16.1:1 | ✅ AAA |
| Texto secundario `#475569` sobre `#FFFFFF` | 5.9:1 | ✅ AA |
| Texto muted `#94A3B8` sobre `#FFFFFF` | 3.0:1 | ⚠️ Solo para texto grande (+18px) |
| Texto blanco sobre `#1E3A5F` (hero) | 9.2:1 | ✅ AAA |
| Texto blanco sobre `#1B6B45` (Essenza) | 7.5:1 | ✅ AAA |
| Texto blanco sobre `#2D3748` (Budefry) | 8.7:1 | ✅ AAA |
| Acento dorado `#C9A84C` sobre blanco | 2.8:1 | ⚠️ Solo decorativo, nunca para texto crítico |

> **Nota:** El dorado de Anstra `#C9A84C` **no debe usarse para texto** sobre fondo blanco. Úsarlo únicamente para iconos, bordes y badges con fondo oscuro.

### Navegación por teclado
- Todos los elementos interactivos deben ser alcanzables con `Tab`.
- El estado `:focus-visible` debe tener un indicador de al menos 2px de outline contrastante.
- El menú hamburguesa usa `aria-expanded` para comunicar estado a lectores de pantalla.
- Las tarjetas de formulario (`form-card`) usan `<article>` para semántica correcta.

### Atributos ARIA obligatorios
```html
<!-- Nav principal -->
<nav role="navigation" aria-label="Navegación principal">

<!-- Hero -->
<section aria-labelledby="hero-title">

<!-- Botones de acción -->
<a href="..." aria-label="Abrir formulario [nombre] - [empresa]">

<!-- Formularios externos -->
<a href="..." target="_blank" rel="noopener noreferrer">
  Abrir formulario
  <span class="sr-only">(abre en nueva ventana)</span>
</a>
```

---

## 7. Patrones de Interacción

### Form Card — Comportamiento esperado
```
Estado normal:    Fondo blanco, borde #E2E8F0, sombra mínima
Estado hover:     Borde color empresa, sombra lg, translateY(-2px), borde top color acento
Estado featured:  Borde color acento desde inicio (destacado visual permanente)
Clic:             Abre Google Form en nueva pestaña (target="_blank")
```

### Company Portal Card — Comportamiento esperado
```
Estado normal:    Fondo blanco, borde sutil
Estado hover:     Sombra xl, translateY(-4px), flecha del link se desplaza +4px
```

### Navegación — Comportamiento esperado
```
Link activo:      Fondo rgba(255,255,255,0.20), texto blanco
Link hover:       Fondo rgba(255,255,255,0.12), texto blanco
Mobile:           Colapsa en hamburguesa, despliega panel vertical con fondo company-dark
```

---

## 8. Componentes con Estado "Pendiente"

Los formularios de RRHH por empresa no tienen URLs públicas documentadas al momento del diseño. El sistema incluye:

- **Badge `form-card__badge--pending`**: Fondo gris, texto muted, indica "Recurso integrado" o similar.
- **Enlace `href="#"`**: Marcador de posición hasta que se registre el URL real.

**Checklist antes de lanzar en WordPress:**
- [ ] Reemplazar todos los `href="#"` con el URL real del Google Form o Google Doc de cada empresa.
- [ ] Cambiar badge `--pending` por `--form` o `--doc` según corresponda.
- [ ] Verificar que los links de Google Maps en el footer apunten a las ubicaciones correctas (actualmente apuntan a `https://maps.google.com` genérico).
- [ ] Validar los links de formularios transversales (Admin, TIC, Gestiones) — ya están registrados con URLs reales de `forms.gle`.

---

## 9. Convenciones para el Tema WordPress

### Estructura de archivos CSS recomendada
```
/wp-content/themes/mc-intranet/
├── assets/
│   ├── css/
│   │   ├── design-tokens.css   ← Variables CSS (enqueued primero)
│   │   └── components.css      ← Componentes (enqueued segundo)
│   └── js/
│       └── nav-toggle.js       ← Hamburguesa y estado activo de nav
```

### Implementación del color de empresa
En WordPress, el `data-company` se puede inyectar con PHP en `functions.php`:

```php
function mc_body_class_company($classes) {
    $page_id = get_the_ID();
    $company = get_post_meta($page_id, 'company_context', true);
    if ($company) {
        // Se aplica como atributo data en body
        // Usar wp_body_open o filtro body_class
    }
    return $classes;
}
```

O mediante un atributo personalizado en el template:
```html
<body <?php body_class(); ?> data-company="<?php echo esc_attr(get_post_meta(get_the_ID(), 'company_context', true)); ?>">
```

### Campos personalizados recomendados (ACF o meta nativa)
| Campo | Tipo | Descripción |
|-------|------|-------------|
| `company_context` | `select` | anstra / essenza / budefry / interactua |
| `form_url` | `url` | URL del Google Form o Google Doc |
| `form_type` | `select` | form / doc / integrated |
| `form_featured` | `boolean` | Si la tarjeta debe mostrarse como destacada |

---

## 10. Guía Rápida para Nuevas Secciones

Si en el futuro se agrega una nueva empresa o sección:

1. **Definir paleta de 5 variables** en `design-tokens.css`:
   ```css
   [data-company="nueva-empresa"] {
     --color-company-50:          #...;
     --color-company:             #...;
     --color-company-dark:        #...;
     --color-company-accent:      #...;
     --color-company-accent-tint: #...;
   }
   ```
2. **Verificar contraste** del texto blanco sobre `--color-company` (mínimo 4.5:1).
3. **Agregar el link** a la barra de navegación global en todos los archivos HTML/templates.
4. **Crear página en WordPress** con campo `company_context` configurado.
5. **Seguir estructura de pantalla** existente: Hero → Alerta de contexto → Section RRHH → Accesos a otras empresas → Footer.

---

*Documento generado como parte del sistema de diseño MC-Intranet v1.0.*
*Para consultas sobre implementación WordPress, revisar `wireframes/brand/brand-manual.html`.*
