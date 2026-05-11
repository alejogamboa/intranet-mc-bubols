# 📦 GUÍA COMPLETA: Despliegue de MC Intranet en A2Hosting (cPanel)

**Versión:** 1.0  
**Fecha:** 8 de mayo de 2026  
**Destino:** A2Hosting con cPanel  
**CMS:** WordPress 6.x + Elementor + ACF Pro + Plugin MC Intranet Core

---

## 📑 Tabla de Contenidos

1. [Requisitos Previos](#requisitos-previos)
2. [Fase 1: Preparación del Código](#fase-1-preparación-del-código)
3. [Fase 2: Configuración en A2Hosting (cPanel)](#fase-2-configuración-en-a2hosting-cpanel)
4. [Fase 3: Subida de Archivos](#fase-3-subida-de-archivos)
5. [Fase 4: Configuración de Base de Datos](#fase-4-configuración-de-base-de-datos)
6. [Fase 5: Configuración de WordPress](#fase-5-configuración-de-wordpress)
7. [Fase 6: Instalación de Plugins y Tema](#fase-6-instalación-de-plugins-y-tema)
8. [Fase 7: Migraciones de Contenido](#fase-7-migraciones-de-contenido)
9. [Fase 8: Configuración de SSL y Dominio](#fase-8-configuración-de-ssl-y-dominio)
10. [Fase 9: Optimización para Producción](#fase-9-optimización-para-producción)
11. [Fase 10: Testing Pre-Lanzamiento](#fase-10-testing-pre-lanzamiento)
12. [Fase 11: Go-Live y Monitoreo](#fase-11-go-live-y-monitoreo)
13. [Mantenimiento Continuo](#mantenimiento-continuo)
14. [Troubleshooting](#troubleshooting)

---

## Requisitos Previos

### A2Hosting (Verificar antes de iniciar)

- ✅ Plan de hosting activo (recomendado: Turbo Singular o superior)
- ✅ cPanel acceso habilitado (usuario: cuenta principal o adicional)
- ✅ PHP 8.1+ instalado y seleccionable
- ✅ MySQL 8.0+ disponible
- ✅ SSL certificado (Let's Encrypt gratuito disponible en cPanel)
- ✅ SSH acceso habilitado (opcional pero recomendado)
- ✅ Cuota de espacio suficiente (mínimo 5 GB recomendado)

### Equipo Local (Para ejecutar migraciones)

- Acceso SFTP o SSH al servidor
- Git (opcional, para control de versiones)
- Herramientas de backup local (backup de la BD local Docker)
- Cliente MySQL (para validaciones)

### Dominio

- Dominio ya registrado y apuntando a A2Hosting
- Acceso a DNS si es necesario cambiar NS records

---

## Fase 1: Preparación del Código

### Paso 1.1: Generar Archivo de Exportación

En el equipo local, prepara el proyecto para exportación:

```bash
# Navega al directorio del proyecto
cd /Users/alejandrogamboa/Documents/bubols-fryscol/intranet/mc-intranet-wordpress

# Crea un directorio temporal para el export
mkdir -p ../export_produccion
cd ../export_produccion
```

### Paso 1.2: Estructura de Archivos a Subir

El servidor de A2Hosting espera esta estructura en la raíz del dominio (`public_html/`):

```
public_html/
├── wp-content/
│   ├── plugins/
│   │   └── mc-intranet-core/          ← Plugin propio
│   ├── themes/
│   │   └── mc_intranet/               ← Tema propio
│   └── uploads/                       ← Contenido multimedia
├── wp-config.php                      ← Configuración de WP (generada aquí)
├── wp-admin/
├── wp-includes/
├── index.php
├── wp-load.php
├── wp-settings.php
└── [otros archivos core de WordPress]
```

### Paso 1.3: Copiar Archivos del Proyecto

Desde el equipo local:

```bash
# Desde: /Users/alejandrogamboa/Documents/bubols-fryscol/intranet

# 1. Copiar plugin mc-intranet-core
cp -r mc-intranet-wordpress/mc-intranet-core /tmp/mc_intranet_deploy/wp-content/plugins/

# 2. Copiar tema WordPress
cp -r "mc-intranet-wordpress/tema wordpress" /tmp/mc_intranet_deploy/wp-content/themes/mc_intranet

# 3. Crear archivo .htaccess (para URLs amigables)
# Se creará en el siguiente paso
```

### Paso 1.4: Generar wp-config.php para Producción

**NO uses el archivo `wp_basics/wp-config.php` tal cual.** Crearemos uno nuevo:

```php
<?php
// wp-config.php para PRODUCCIÓN en A2Hosting
// Generado: 2026-05-08

// ════════════════════════════════════════════════════════════════
// CREDENCIALES DE BASE DE DATOS (A2Hosting proporcionará estas)
// ════════════════════════════════════════════════════════════════

define( 'DB_NAME',     'tu_db_produccion' );      // Reemplazar con la BD real
define( 'DB_USER',     'tu_db_usuario' );         // Reemplazar con usuario real
define( 'DB_PASSWORD', 'tu_db_password' );        // Reemplazar con contraseña real
define( 'DB_HOST',     'localhost' );             // Generalmente localhost en A2Hosting
define( 'DB_CHARSET', 'utf8mb4' );
define( 'DB_COLLATE',  'utf8mb4_unicode_ci' );

// ════════════════════════════════════════════════════════════════
// SEGURIDAD
// ════════════════════════════════════════════════════════════════
// Genera nuevas claves en: https://api.wordpress.org/secret-key/1.1/salt/

define( 'AUTH_KEY',         'poner-clave-generada-aqui' );
define( 'SECURE_AUTH_KEY',  'poner-clave-generada-aqui' );
define( 'LOGGED_IN_KEY',    'poner-clave-generada-aqui' );
define( 'NONCE_KEY',        'poner-clave-generada-aqui' );
define( 'AUTH_SALT',        'poner-clave-generada-aqui' );
define( 'SECURE_AUTH_SALT', 'poner-clave-generada-aqui' );
define( 'LOGGED_IN_SALT',   'poner-clave-generada-aqui' );
define( 'NONCE_SALT',       'poner-clave-generada-aqui' );

// ════════════════════════════════════════════════════════════════
// CONFIGURACIÓN DE SITIO
// ════════════════════════════════════════════════════════════════

// Reemplazar con tu dominio real
define( 'WP_HOME',   'https://intranet.tudominio.com' );
define( 'WP_SITEURL', 'https://intranet.tudominio.com' );

// ════════════════════════════════════════════════════════════════
// MODO DEBUG Y LOGGING
// ════════════════════════════════════════════════════════════════

// En PRODUCCIÓN: false
define( 'WP_DEBUG', false );

// Logs de errores de PHP (recomendado para monitoreo)
define( 'WP_DEBUG_DISPLAY', false );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_LOG_FILE', WP_CONTENT_DIR . '/debug.log' );

// ════════════════════════════════════════════════════════════════
// OPTIMIZACIÓN Y SEGURIDAD
// ════════════════════════════════════════════════════════════════

// Deshabilitar edición de archivos desde dashboard (recomendado)
define( 'DISALLOW_FILE_EDIT', true );
define( 'DISALLOW_FILE_MODS', true );

// Aumentar límite de memoria (Elementor y ACF pueden requerirlo)
define( 'WP_MEMORY_LIMIT', '256M' );
define( 'WP_MAX_MEMORY_LIMIT', '512M' );

// Prefijo de tabla (por seguridad, puedes cambiar si lo deseas)
$table_prefix = 'wp_';

// ════════════════════════════════════════════════════════════════
// ABSPATH Y CARGA
// ════════════════════════════════════════════════════════════════

if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

require_once( ABSPATH . 'wp-settings.php' );
```

### Paso 1.5: Crear Archivo .htaccess para URLs Amigables

Crea el archivo `.htaccess` en la raíz (`public_html/.htaccess`):

```apache
# BEGIN WordPress
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase /
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]
</IfModule>
# END WordPress

# Seguridad: Denegar acceso a archivos sensibles
<FilesMatch "^.*\.php$">
    Order Deny,Allow
    Deny from all
</FilesMatch>

<Files ~ "\.php$">
    Allow from all
</Files>

# Evitar listado de directorios
Options -Indexes

# Protección: Denegar acceso a wp-config.php
<Files wp-config.php>
    Order allow,deny
    Deny from all
</Files>

# Deshabilitar acceso directo a wp-cli
<Files wp-cli.phar>
    Order allow,deny
    Deny from all
</Files>
```

### Paso 1.6: Preparar Lista de Plugins Requeridos

Los plugins deben instalarse en cPanel/WordPress después del despliegue. Aquí la lista:

| Plugin | Tipo | Versión | Uso |
|--------|------|---------|-----|
| Elementor | Free/Pro | Última | Page Builder |
| Advanced Custom Fields PRO | Premium | Última | Campos personalizados |
| MC Intranet Core | Propio | 1.0.0 | Plugin de negocio (se copia) |
| UpdraftPlus | Free | Última | Backups programados |
| WP Activity Log | Free | Última | Auditoría de cambios |
| Wordfence Security | Free | Última | Seguridad |
| WP Rocket o LiteSpeed Cache | Premium/Free | Última | Cache y optimización |
| User Role Editor | Free | Última | Gestión de roles |

---

## Fase 2: Configuración en A2Hosting (cPanel)

### Paso 2.1: Acceder a cPanel

1. Abre: `https://intranet.tudominio.com:2083` (o `https://tu-cuenta.a2hosting.com:2083`)
2. Ingresa credenciales de cPanel (usuario y contraseña principal)
3. Verifica que estés en el dominio correcto (esquina superior izquierda)

### Paso 2.2: Crear Base de Datos

En cPanel, vamos a **Bases de datos MySQL**:

1. **Crear nueva base de datos:**
   - Nombre sugerido: `intranet_prod` (cPanel agregará prefijo de cuenta)
   - Nombre final será algo como: `cuenta_intranet_prod`
   - Guarda el nombre exacto

2. **Crear usuario de BD:**
   - Usuario: `intranet_user` (cPanel agregará prefijo)
   - Contraseña: Genera una fuerte (usa el generador de cPanel)
   - Guarda usuario y contraseña exactos

3. **Asignar permisos:**
   - Selecciona el usuario → el usuario
   - Asigna todos los privilegios (All Privileges)
   - Haz clic en "Cambiar privilegios"

**Nota importante:** Los valores finales serán:
```
Database: cuenta_intranet_prod
User: cuenta_intranet_user
Password: [tu-contraseña-generada]
Host: localhost
```

### Paso 2.3: Configurar PHP

En cPanel, ve a **Seleccionar versión de PHP**:

1. Asegúrate de que **PHP 8.1 o superior** esté seleccionado
2. Si no aparece 8.1+, contacta a A2Hosting (debe estar disponible en Turbo)

En cPanel, ve a **Opciones de PHP**:

1. Habilita las siguientes extensiones:
   - ✅ curl
   - ✅ gd
   - ✅ mbstring
   - ✅ mysql / mysqli
   - ✅ xml
   - ✅ zip
   - ✅ json (casi siempre por defecto)

2. Ajusta parámetros:
   ```
   memory_limit = 256M
   post_max_size = 128M
   upload_max_filesize = 128M
   max_execution_time = 300
   max_input_time = 300
   ```

### Paso 2.4: Crear Cuenta de SFTP (Opcional pero Recomendado)

En cPanel, ve a **Cuentas de FTP**:

1. Crea una nueva cuenta o usa la principal
2. Directorio: `/public_html`
3. Guarda credenciales:
   ```
   Host: ftp.tudominio.com (o sftp.tudominio.com)
   Usuario: tu-usuario-sftp
   Contraseña: [contraseña]
   Puerto: 21 (FTP) o 22 (SFTP)
   ```

### Paso 2.5: Habilitar SSL Let's Encrypt

En cPanel, ve a **AutoSSL** (o **SSL/TLS Status**):

1. Si está disponible, haz clic en "Instalar"
2. Selecciona tu dominio
3. Espera a que se emita el certificado (minutos a horas)
4. Verifica que aparezca como "Activo"

---

## Fase 3: Subida de Archivos

### Paso 3.1: Opción A - Subir WordPress Core

**Si A2Hosting no incluye instalador automático de WordPress:**

En cPanel, ve a **Instalador de Aplicaciones (Softaculous)** o **Instalador automático**:

1. Busca "WordPress"
2. Haz clic en "Instalar"
3. Configura:
   - **Protocolo:** https://
   - **Dominio:** intranet.tudominio.com (sin www salvo que se desee)
   - **Directorio:** Déjalo en blanco (instalará en raíz `public_html/`)
   - **Nombre del sitio:** MC Intranet
   - **Descripción:** Intranet Corporativa MC
   - **Usuario admin:** admin-intranet (no usar "admin")
   - **Email admin:** tu-email@empresa.com
   - **Contraseña admin:** [fuerte]

4. Completa la instalación

Esto crea la estructura WordPress automáticamente en `public_html/`.

### Paso 3.2: Opción B - Subir WordPress Manually (Si es necesario)

Si prefieres control total:

1. Descarga WordPress core desde [wordpress.org](https://wordpress.org/download/)
2. Extrae localmente
3. Sube vía SFTP a `public_html/` (todos los archivos excepto `wp-config.php`)

### Paso 3.3: Subir Plugin y Tema

Vía SFTP (recomendado) o cPanel File Manager:

```bash
# Estructura final esperada en public_html/

public_html/
└── wp-content/
    ├── plugins/
    │   └── mc-intranet-core/
    │       ├── mc-intranet-core.php
    │       ├── includes/
    │       ├── templates/
    │       └── ...
    ├── themes/
    │   └── mc_intranet/
    │       ├── style.css
    │       ├── functions.php
    │       ├── header.php
    │       ├── front-page.php
    │       ├── assets/
    │       └── ...
    └── uploads/
        └── [vacío o con contenido migrado]
```

**Subida con SFTP:**

Usa cliente como FileZilla, Transmit o terminal:

```bash
# Desde equipo local
sftp usuario@sftp.tudominio.com

# En sesión SFTP
cd public_html/wp-content/plugins/
put -r /path/local/mc-intranet-core

cd ../themes/
put -r /path/local/"tema wordpress" mc_intranet
```

### Paso 3.4: Subir wp-config.php

Sube el archivo `wp-config.php` que preparaste en Fase 1.4:

```bash
# Vía SFTP
cd public_html/
put /path/local/wp-config.php
```

**O manualmente en cPanel File Manager:**

1. Ve a `public_html/`
2. Crea un archivo nuevo: `wp-config.php`
3. Pega el contenido que preparaste (con credenciales reales)

---

## Fase 4: Configuración de Base de Datos

### Paso 4.1: Exportar Base de Datos Local (Docker)

Desde el equipo local, exporta la BD de desarrollo:

```bash
# Conecta a MySQL en Docker
cd /Users/alejandrogamboa/Documents/bubols-fryscol/intranet/mc-intranet-wordpress

# Exporta la BD
docker compose exec db mysqldump -u wordpress_user -psecure_password wordpress > /tmp/wordpress_backup.sql
```

Esto crea un archivo SQL con toda la estructura y datos.

### Paso 4.2: Importar Base de Datos en A2Hosting

En cPanel, ve a **phpMyAdmin**:

1. Selecciona tu base de datos (creada en Fase 2.2)
2. Ve a la pestaña **Importar**
3. Haz clic en **Elegir archivo** y selecciona tu SQL exportado
4. Haz clic en **Ejecutar**
5. Espera a que termine (puede tardar minutos según tamaño)

### Paso 4.3: Actualizar URLs en Base de Datos

Si el dominio local (`http://localhost:8000`) es diferente del de producción:

En phpMyAdmin, en tu BD, ejecuta estas queries en la pestaña **SQL**:

```sql
-- Reemplaza URLs en base de datos
UPDATE wp_options SET option_value = REPLACE(option_value, 'http://localhost:8000', 'https://intranet.tudominio.com') WHERE option_name IN ('siteurl', 'home');

UPDATE wp_posts SET post_content = REPLACE(post_content, 'http://localhost:8000', 'https://intranet.tudominio.com');

UPDATE wp_postmeta SET meta_value = REPLACE(meta_value, 'http://localhost:8000', 'https://intranet.tudominio.com');

UPDATE wp_options SET option_value = REPLACE(option_value, 'http://localhost:8000', 'https://intranet.tudominio.com');
```

---

## Fase 5: Configuración de WordPress

### Paso 5.1: Acceder a WordPress

Abre: `https://intranet.tudominio.com/wp-admin`

1. Ingresa con las credenciales admin creadas en Fase 3.1
2. Verifica que estés en el dashboard correcto

### Paso 5.2: Verificar Ajustes Generales

Ve a **Ajustes → General**:

- **Título del sitio:** MC Intranet
- **Descripción corta:** Intranet Corporativa
- **URL de WordPress (WP_SITEURL):** `https://intranet.tudominio.com`
- **URL del sitio (WP_HOME):** `https://intranet.tudominio.com`
- **Zona horaria:** America/Bogota (o la correcta)
- **Formato de fecha/hora:** Según preferencia

### Paso 5.3: Configurar Estructura de Enlaces Permanentes

Ve a **Ajustes → Enlaces Permanentes**:

1. Selecciona **Nombre de entrada** (o `/postname/`)
2. Haz clic en **Guardar cambios**
3. Si aparece error de `.htaccess`, copia el código que ofrece WordPress al `.htaccess` en `public_html/`

### Paso 5.4: Configurar Lectura

Ve a **Ajustes → Lectura**:

- **Portada muestra:** Una página estática
- **Portada:** Selecciona tu página de inicio (creada desde contenido)
- **Página de las entradas:** [sin entradas de blog, dejar vacío]

### Paso 5.5: Configurar Comentarios y Discusiones

Ve a **Ajustes → Discusiones**:

Para una intranet corporativa (sin comentarios públicos):

- ☐ Desmarcar: "Permitir comentarios en artículos nuevos"
- ☐ Desmarcar: "Permitir que las personas envíen comentarios en artículos nuevos"

---

## Fase 6: Instalación de Plugins y Tema

### Paso 6.1: Activar Tema

En WordPress, ve a **Apariencia → Temas**:

1. Verifica que aparezca **MC Intranet** en la galería
2. Haz clic en **Activar**

### Paso 6.2: Instalar Plugins (Vía Dashboard o SFTP)

#### Instalación Vía Dashboard (Recomendado):

Ve a **Plugins → Añadir nuevo**:

**Para plugins de repositorio oficial:**

1. Busca el nombre (ej: "Elementor")
2. Haz clic en **Instalar**
3. Luego en **Activar**

**Para plugins premium (si tienes licencia):**

1. Descarga el ZIP desde el sitio del proveedor
2. En WordPress, ve a **Plugins → Añadir nuevo → Subir plugin**
3. Selecciona el ZIP
4. Haz clic en **Instalar ahora**
5. Luego en **Activar**

#### Plugins a Instalar (En orden):

| Paso | Plugin | Tipo | Acción |
|------|--------|------|--------|
| 1 | Elementor | Free | Buscar → Instalar → Activar |
| 2 | Advanced Custom Fields PRO | Premium | Subir → Instalar → Activar |
| 3 | UpdraftPlus | Free | Buscar → Instalar → Activar |
| 4 | WP Activity Log | Free | Buscar → Instalar → Activar |
| 5 | User Role Editor | Free | Buscar → Instalar → Activar |
| 6 | Wordfence Security | Free | Buscar → Instalar → Activar |
| 7 | WP Rocket o LiteSpeed Cache | Premium/Free | Según disponibilidad |

### Paso 6.3: Verificar Activación de Plugin Propio

En WordPress, ve a **Plugins**:

- Debe aparecer **MC Intranet Core** como activo
- Si no aparece, verifica que esté en `wp-content/plugins/mc-intranet-core/`

---

## Fase 7: Migraciones de Contenido

### Paso 7.1: Estructura Base de Contenido

Según el PRD, crea páginas estáticas principales:

En WordPress, ve a **Páginas → Añadir nueva**:

1. **Inicio**
   - Plantilla: Elementor Full Width o Blanca
   - Slug: `/`
   - Contenido: Será construido con Elementor (hero, tarjetas de empresa, etc.)

2. **Administración** (una por sección)
   - Slug: `/administracion/`
   - Contenido: Formularios y enlaces a Google Forms

3. **TIC**
   - Slug: `/tic/`

4. **Gestiones**
   - Slug: `/gestiones/`

5. **Projection Anstra** (página por empresa)
   - Slug: `/empresa/projection-anstra/`
   - Contenido: Información y formularios de Projection Anstra

6. **Essenza Foods**
   - Slug: `/empresa/essenza-foods/`

7. **Budefry**
   - Slug: `/empresa/budefry/`

8. **Interactua** (Cultura)
   - Slug: `/interactua/`

### Paso 7.2: Importar Contenido con XML (Si existe)

Si exportaste un XML de WordPress local:

En WordPress, ve a **Herramientas → Importar**:

1. Busca WordPress
2. Haz clic en **Instalar importador**
3. Carga tu archivo `.xml`
4. Mapea usuarios y haz clic en **Enviar**

### Paso 7.3: Refrescar ACF

Si usas ACF Pro:

En WordPress, ve a **ACF → Tools**:

1. Haz clic en **Sincronizar** (si hay cambios en código)
2. Verifica que todos los grupos de campos aparezcan

### Paso 7.4: Importar Plantillas Elementor

Si tienes plantillas guardadas:

En WordPress, ve a **Elementor → Plantillas**:

1. Haz clic en **Importar plantillas**
2. Carga archivos `.json` de tus plantillas
3. Luego asigna a páginas según corresponda

---

## Fase 8: Configuración de SSL y Dominio

### Paso 8.1: Verificar SSL

En cPanel, ve a **SSL/TLS Status**:

1. Verifica que tu dominio muestre ✅ "Activo"
2. Si no está activo, ve a **AutoSSL** y haz clic en "Instalar"

### Paso 8.2: Forzar HTTPS

En cPanel, ve a **Force HTTPS Redirect**:

1. Selecciona tu dominio
2. Haz clic en **Install**

O manualmente, añade esto al `.htaccess`:

```apache
# Forzar HTTPS
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>
```

### Paso 8.3: Redirigir www a sin-www (Opcional)

En WordPress, **Ajustes → General**, asegúrate de que ambas URLs (con y sin www) sean iguales.

---

## Fase 9: Optimización para Producción

### Paso 9.1: Activar Cache

Instala **WP Rocket** o **LiteSpeed Cache** (según disponibilidad en A2Hosting):

En WordPress, ve a **[Plugin] → Settings**:

- Habilita cache de página
- Habilita lazy loading
- Habilita minificación de CSS/JS
- Habilita compresión GZIP

### Paso 9.2: Optimizar Imágenes

En WordPress, ve a **Biblioteca multimedia**:

1. Redimensiona todas las imágenes a máximo 1200px ancho
2. Usa formato WebP si es posible
3. Comprime con herramienta como TinyPNG o Imagify

### Paso 9.3: Configurar Backups Automáticos

Instala **UpdraftPlus**:

En WordPress, ve a **Ajustes → UpdraftPlus**:

1. **Destino de almacenamiento:** Google Drive o Dropbox (recomendado)
2. **Frecuencia de backups:** Diariamente
3. **Retención:** Mantén últimos 30 días
4. Haz clic en **Guardar cambios**

### Paso 9.4: Configurar Monitoreo de Seguridad

Instala **Wordfence**:

En WordPress, ve a **Wordfence → All Options**:

1. **Email de alertas:** tu-email@empresa.com
2. **Monitorar cambios de archivos:** Sí
3. **Bloquear accesos fallidos:** Sí (máximo 20 intentos)

### Paso 9.5: Desactivar el Editor de Código

En WordPress, ve a **Plugins** y desactiva:

- "Code Snippets" u otros que permitan editar PHP directamente

O en el `wp-config.php` ya está configurado:

```php
define( 'DISALLOW_FILE_EDIT', true );
define( 'DISALLOW_FILE_MODS', true );
```

### Paso 9.6: Ocultar Versión de WordPress

En el `wp-config.php`, añade:

```php
define( 'WP_ENV', 'production' );
```

En `functions.php` del tema, añade:

```php
// Ocultar versión de WordPress
remove_action( 'wp_head', 'wp_generator' );
```

---

## Fase 10: Testing Pre-Lanzamiento

### Paso 10.1: Testing Funcional

**En navegador privado o diferente de tu sesión admin:**

1. ✅ Accede a `https://intranet.tudominio.com` (homepage carga)
2. ✅ Verifica que todas las secciones (Administración, TIC, Gestiones, etc.) carguen
3. ✅ Prueba navegación en desktop
4. ✅ Prueba navegación en móvil
5. ✅ Verifica que enlaces externos (Google Forms, Google Drive, etc.) funcionen

### Paso 10.2: Testing de Seguridad

1. ✅ Intenta acceder a `https://intranet.tudominio.com/wp-config.php` (debe negar acceso)
2. ✅ Verifica que SSL sea válido (navegador muestra candado verde)
3. ✅ En Wordfence, chequea **Alertas de seguridad**

### Paso 10.3: Testing de Performance

Usa herramientas gratuitas:

- **Google PageSpeed Insights:** `https://pagespeed.web.dev/`
  - Ingresa tu URL
  - Verifica que Core Web Vitals sean Green/Good

- **GTmetrix:** `https://gtmetrix.com/`
  - Verifica tiempo de carga (objetivo < 3 segundos)

### Paso 10.4: Testing de Responsividad

En navegador (F12 → Toggle Device Toolbar):

- ✅ Pantalla 320px (móvil pequeño)
- ✅ Pantalla 768px (tablet)
- ✅ Pantalla 1920px (desktop)

### Paso 10.5: Testing de Contenido

Verifica que todo el contenido migrado sea correcto:

- ✅ Todas las páginas tengan contenido
- ✅ Imágenes carguen
- ✅ Formularios embebidos funcionen
- ✅ Metadatos (title, description) sean los esperados

---

## Fase 11: Go-Live y Monitoreo

### Paso 11.1: Pasos Finales Antes del Go-Live

**24 horas antes:**

1. ✅ Verifica que DNS apunte a A2Hosting
2. ✅ Confirma que SSL está activo
3. ✅ Realiza backup completo con UpdraftPlus
4. ✅ Verifica que base de datos tenga respaldo en otro lugar

### Paso 11.2: Anuncio a Usuarios

Prepara un email de comunicación:

```
Asunto: 🚀 MC Intranet en producción

Estimados colaboradores,

Les informamos que nuestra nueva Intranet Corporativa ya está disponible en:

👉 https://intranet.tudominio.com

Características:
- Acceso centralizado a formularios y recursos
- Interfaz mejorada y responsiva
- Información por empresa (Projection Anstra, Essenza Foods, Budefry)

Si encuentran algún problema, reporten a: [email de contacto técnico]

¡Bienvenidos a la nueva intranet!
```

### Paso 11.3: Monitoreo Inicial (Primeros 7 días)

**Diariamente:**

- En cPanel, revisa **Estadísticas** (CPU, memoria, transferencia)
- En WordPress, ve a **Wordfence → Activity** (alertas de seguridad)
- En WordPress, ve a **UpdraftPlus** (confirma que backups se ejecutan)

**Semanalmente:**

- Ejecuta Google PageSpeed Insights
- Verifica logs de error en `wp-content/debug.log`
- Recopila feedback de usuarios

### Paso 11.4: Escalación de Problemas

Si ocurren problemas:

1. **Error 500 (Server Error):**
   - En cPanel, ve a **Error Log** (última hora)
   - Busca errores relacionados con PHP
   - Si es por memoria, aumenta `memory_limit` en PHP Options

2. **Página lenta:**
   - Ejecuta Google PageSpeed
   - Verifica en UpdraftPlus que los backups no estén ejecutándose
   - En WP Rocket, verifica que cache esté funcionando

3. **Error de Base de Datos:**
   - En cPanel, ve a **Bases de datos MySQL**
   - Verifica que la BD y usuario existan
   - En phpMyAdmin, verifica que la conexión sea válida

4. **No carga contenido dinámico (ACF/Elementor):**
   - En WordPress, ve a **Plugins** y desactiva/reactiva
   - Verifica que ACF tenga licencia válida
   - En cPanel, aumenta `max_execution_time` a 300 segundos

---

## Mantenimiento Continuo

### Checklist Mensual

- [ ] Verificar que backups automáticos se ejecuten
- [ ] Actualizar WordPress core (si hay parches de seguridad)
- [ ] Actualizar plugins (después de verificar compatibilidad en staging)
- [ ] Revisar Wordfence Activity Log
- [ ] Limpiar `wp-content/debug.log` si crece mucho

### Checklist Trimestral

- [ ] Revisar logs de acceso (estadísticas de uso)
- [ ] Limpiar base de datos de revisiones viejas (usar WP-Optimize)
- [ ] Actualizar ACF Pro si hay nuevas versiones
- [ ] Revisar performance con PageSpeed Insights

### Actualizaciones de Plugins

**NUNCA actualices directamente en producción.** Proceso recomendado:

1. En entorno local (Docker), actualiza y prueba
2. Commit en Git si usas control de versiones
3. Subir cambios a staging (si tienes)
4. Luego, en producción, actualizar

---

## Troubleshooting

### Problema: "Error estableciendo conexión con la base de datos"

**Solución:**

1. En cPanel, ve a **Bases de datos MySQL**
2. Verifica que el usuario exista y esté asignado a la BD
3. Verifica credenciales en `wp-config.php`
4. En phpMyAdmin, intenta conectar manualmente para verificar credenciales
5. Si persiste, contacta a A2Hosting support@a2hosting.com

### Problema: "Memoria agotada (Allowed memory size of X bytes exhausted)"

**Solución:**

1. En cPanel, ve a **Opciones de PHP**
2. Aumenta `memory_limit` a 256M o 512M
3. Reactiva WordPress

Si persiste:

- Desactiva algunos plugins que consuman mucha memoria (ej: plugins de backup)
- Limpia `wp-content/debug.log`

### Problema: "La conexión fue restablecida" o "ERR_NETWORK_CHANGED"

**Solución:**

1. Verifica que A2Hosting esté operativo (status.a2hosting.com)
2. Limpia cache del navegador (Ctrl+Shift+Delete)
3. En WordPress, ve a **WP Rocket → Purge Cache**
4. Si persiste, contacta a A2Hosting

### Problema: "Elementor: Failed to load page"

**Solución:**

1. Aumenta `max_execution_time` a 300 en PHP Options
2. Aumenta `post_max_size` a 128M
3. Desactiva y reactiva Elementor
4. En cPanel, revisa **Error Log**

### Problema: "ACF: Invalid license key"

**Solución:**

1. En WordPress, ve a **ACF → License**
2. Ingresa nuevamente la clave de licencia
3. Si es nueva instancia, vincula la licencia en el sitio de ACF
4. Espera 24 horas a que se sincronice

### Problema: "404 en páginas (solo index.php funciona)"

**Solución:**

1. Ve a **Ajustes → Enlaces permanentes**
2. Cambia a **Nombre de entrada** y **Guarda**
3. Verifica que `.htaccess` en `public_html/` tenga el código correcto
4. Si no existe, crea uno manualmente con el código de Fase 1.5
5. En cPanel, verifica que `mod_rewrite` esté habilitado

### Problema: "SSL Mixed Content Warning (navegador muestra advertencia)"

**Solución:**

1. Ejecuta esta query en phpMyAdmin:

```sql
UPDATE wp_options 
SET option_value = REPLACE(option_value, 'http://', 'https://') 
WHERE option_name IN ('siteurl', 'home');

UPDATE wp_posts 
SET post_content = REPLACE(post_content, 'http://', 'https://');

UPDATE wp_postmeta 
SET meta_value = REPLACE(meta_value, 'http://', 'https://');
```

2. Reactiva WordPress
3. Limpia cache del navegador

---

## Contacto y Soporte

### A2Hosting Support

- **Email:** support@a2hosting.com
- **Chat en vivo:** https://www.a2hosting.com/
- **Teléfono:** [según región]

### Equipo Técnico MC

- **Email técnico:** [tu-email-support@empresa.com]
- **Escalación urgente:** [phone o Slack]

---

## Apéndice: Archivos de Referencia

### A.1 Estructura de Directorios Final

```
intranet.tudominio.com (public_html/)
├── index.php
├── wp-load.php
├── wp-config.php                    ← Configuración (credenciales)
├── .htaccess                        ← URLs amigables y seguridad
├── wp-admin/
├── wp-includes/
├── wp-content/
│   ├── plugins/
│   │   ├── mc-intranet-core/        ← Plugin propio (1.0.0)
│   │   ├── elementor/
│   │   ├── advanced-custom-fields-pro/
│   │   ├── updraftplus/
│   │   ├── wordfence/
│   │   └── ... [otros plugins]
│   ├── themes/
│   │   ├── mc_intranet/             ← Tema propio
│   │   │   ├── style.css
│   │   │   ├── functions.php
│   │   │   ├── assets/
│   │   │   └── ...
│   │   └── ... [otros temas]
│   ├── uploads/
│   │   └── [imágenes y archivos de usuarios]
│   └── debug.log                    ← Logs de errores de WordPress
└── ... [otros archivos WordPress]
```

### A.2 Credenciales de Seguridad (Guardar en lugar seguro)

Crea un documento con esto y guárdalo en gestor de contraseñas (LastPass, 1Password, etc.):

```
PRODUCCIÓN - MC INTRANET

Dominio: intranet.tudominio.com
Panel cPanel: https://intranet.tudominio.com:2083
Usuario cPanel: [usuario]
Contraseña cPanel: [contraseña]

Base de Datos:
- Nombre: [nombre completo]
- Usuario: [usuario]
- Contraseña: [contraseña]
- Host: localhost

WordPress Admin:
- URL: https://intranet.tudominio.com/wp-admin
- Usuario: admin-intranet
- Contraseña: [contraseña]

SFTP/FTP:
- Host: sftp.tudominio.com o ftp.tudominio.com
- Usuario: [usuario]
- Contraseña: [contraseña]
- Puerto: 22 (SFTP) o 21 (FTP)

Email de alertas: [email]
```

### A.3 Versiones Verificadas

| Componente | Versión | Fecha Verificación |
|------------|---------|-------------------|
| PHP | 8.1+ | 2026-05-08 |
| MySQL | 8.0+ | 2026-05-08 |
| WordPress | 6.4+ | 2026-05-08 |
| Elementor | Última | 2026-05-08 |
| ACF Pro | Última | 2026-05-08 |
| MC Intranet Core | 1.0.0 | 2026-05-08 |

---

**Documento generado:** 8 de mayo de 2026  
**Responsable:** Equipo de Desarrollo MC  
**Última actualización:** 2026-05-08

