# PRD 4 — Control de acceso privado para MC Intranet

| Campo | Valor |
|---|---|
| Version | 0.1 |
| Fecha | 30 de abril de 2026 |
| Estado | En implementación |
| Responsable funcional | Equipo MC |
| Responsable técnico | MC Intranet Core |

---

## 1. Objetivo

Restringir el acceso al contenido de la intranet para que solo puedan visualizarlo usuarios autenticados con permiso interno aprobado. Cuando un visitante intente ingresar a páginas como inicio, Budefry, Essenza, Projection Anstra o cualquier otra página protegida, deberá ser redirigido a una pantalla personalizada de login. Si el usuario inicia sesión pero no tiene permiso de acceso, deberá visualizar una pantalla de acceso denegado.

---

## 2. Problema actual

La intranet actualmente renderiza contenido público en el frontend y no tiene una capa global de control de acceso. Adicionalmente, el plugin `mc-intranet-core` exponía sus custom post types y taxonomías por REST aunque el frontend del MVP no depende de esa API para mostrar contenido.

Esto genera dos riesgos:

1. Cualquier visitante puede acceder por URL directa al contenido de la intranet.
2. Parte del contenido puede ser enumerado por `wp-json` aunque no exista navegación pública explícita.

---

## 3. Resultado esperado

1. Un visitante no autenticado que intente abrir `/`, `/anstra/`, `/essenza/`, `/budefry/`, `/interactua/` o cualquier otra ruta protegida debe ser redirigido a `/ingresar/`.
2. La pantalla `/ingresar/` debe usar una interfaz propia, pero autenticación nativa de WordPress.
3. Si el login es exitoso, el usuario debe volver a la URL solicitada originalmente.
4. Si el usuario ya inició sesión pero no tiene permiso de acceso, debe ser redirigido a `/acceso-denegado/`.
5. `wp-admin`, `wp-login.php`, recuperación de contraseña, cron y WP-CLI deben seguir funcionando sin interferencias.
6. El contenido protegido no debe quedar expuesto por REST en el MVP.

---

## 4. Decisiones cerradas

1. La autenticación será nativa de WordPress.
2. La experiencia de login será una pantalla personalizada dentro del sitio.
3. La autorización no se amarrará a un nombre de rol fijo; se controlará con la capacidad `access_mc_intranet`.
4. Como conjunto inicial, la capacidad se otorgará a `subscriber`, `editor` y `administrator`.
5. La lógica de seguridad vivirá en el plugin `mc-intranet-core`, no en el tema.
6. Los CPT y taxonomías protegidos no usarán REST público mientras el MVP no lo necesite.

---

## 5. Alcance funcional

### Incluye

1. Guard global del frontend mediante hook de WordPress en el plugin core.
2. Página automática de login con slug `/ingresar/`.
3. Página automática de acceso denegado con slug `/acceso-denegado/`.
4. Redirección con `redirect_to` para conservar el destino original.
5. Mensaje de error visible cuando el login falle desde la pantalla personalizada.
6. Cierre de exposición REST para `mc_formulario`, `mc_evento`, `mc_reconocimiento`, `mc_directorio`, `mc_sede`, `mc_empresa` y `mc_area`.

### No incluye

1. SSO o autenticación externa.
2. Gestión administrativa de permisos por UI propia.
3. Flujos de aprobación de acceso.
4. Integración con proveedores externos de identidad.

---

## 6. Requisitos funcionales

### RF-01 — Redirección de acceso anónimo

Cuando un usuario no autenticado intente ingresar a una ruta protegida del frontend, el sistema debe redirigirlo a la pantalla personalizada de login.

### RF-02 — Login personalizado sobre backend nativo

La pantalla de login debe mostrar un formulario visual propio, pero enviar y validar credenciales con el flujo nativo de WordPress.

### RF-03 — Retorno al destino original

Después de un login exitoso, el sistema debe devolver al usuario a la página que intentó abrir antes de autenticarse.

### RF-04 — Acceso denegado para usuarios autenticados sin permiso

Si el usuario ya está autenticado pero no tiene la capacidad `access_mc_intranet`, el sistema debe redirigirlo a una pantalla de acceso denegado.

### RF-05 — Compatibilidad con operación administrativa

La solución no debe romper acceso a `wp-admin`, login administrativo, recuperación de contraseña, cron ni WP-CLI.

### RF-06 — Protección de contenido fuera del HTML

Los contenidos protegidos del plugin core no deben seguir expuestos por REST en el MVP.

---

## 7. Requisitos no funcionales

1. La implementación debe mantenerse dentro de la arquitectura actual: negocio en plugin, presentación en tema.
2. La solución debe ser compatible con WordPress actual y PHP 8.1+ del proyecto.
3. El flujo debe ser comprensible en móvil y escritorio.
4. El código debe ser mantenible, centralizando la seguridad en un único módulo.
5. El sistema debe evitar loops de redirección entre login, acceso denegado e inicio.

---

## 8. Diseño técnico aprobado

### Plugin core

Se implementará un módulo dedicado de control de acceso dentro de `mc-intranet-core` para centralizar:

1. Evaluación de acceso por capacidad.
2. Redirección de visitantes anónimos.
3. Redirección de usuarios autenticados sin permiso.
4. Creación automática de páginas de sistema (`/ingresar/`, `/acceso-denegado/`).
5. Compatibilidad con `redirect_to`.

### Tema

El tema solo aportará estilos visuales para las pantallas de login y acceso denegado. No contendrá lógica de autorización.

---

## 9. Estado actual de implementación

### Ya implementado

1. Nuevo módulo `class-access-control.php` en el plugin core.
2. Registro del módulo desde el bootstrap del plugin.
3. Creación automática de capacidad `access_mc_intranet` para roles base definidos.
4. Protección global del frontend mediante `template_redirect`.
5. Páginas automáticas `/ingresar/` y `/acceso-denegado/`.
6. Shortcodes `[mc_login_screen]` y `[mc_access_denied]`.
7. Cierre de `show_in_rest` en CPT y taxonomías protegidas.
8. Estilos base para la experiencia de autenticación en el tema.

### Pendiente por validar en entorno WordPress

1. Confirmar que las páginas de sistema se crean correctamente al activar o cargar el plugin como administrador.
2. Confirmar navegación real con usuarios `subscriber`, `editor`, `administrator` y un usuario sin capacidad.
3. Verificar si existen roles custom en producción que también deban recibir `access_mc_intranet`.
4. Ajustar copy final del mensaje de acceso denegado y correo/mesa de ayuda si negocio define uno distinto.

---

## 10. Criterios de aceptación

1. Visitante anónimo en `/` termina en `/ingresar/`.
2. Visitante anónimo en `/anstra/` termina en `/ingresar/`.
3. Visitante anónimo en `/essenza/` termina en `/ingresar/`.
4. Visitante anónimo en `/budefry/` termina en `/ingresar/`.
5. Visitante anónimo en `/interactua/` termina en `/ingresar/`.
6. Usuario con `access_mc_intranet` inicia sesión y vuelve al destino original.
7. Usuario autenticado sin `access_mc_intranet` termina en `/acceso-denegado/`.
8. `wp-admin` sigue disponible para administración.
9. Los endpoints REST de CPT y taxonomías protegidas no quedan expuestos públicamente.
10. La pantalla personalizada de login funciona en móvil y escritorio.

---

## 11. Casos de prueba mínimos

1. Acceder a `/` sin sesión.
2. Acceder a una página de empresa sin sesión.
3. Abrir `/ingresar/`, fallar login y validar mensaje.
4. Abrir `/ingresar/`, autenticar y validar retorno.
5. Iniciar sesión con usuario sin capacidad y validar acceso denegado.
6. Consultar `wp-json/wp/v2/mc_formulario` y validar que no haya exposición pública en el MVP.
7. Abrir `wp-admin` con administrador y validar operación normal.

---

## 12. Riesgos y supuestos

1. Si existen roles custom no inventariados, algunos usuarios internos podrían quedar sin acceso hasta asignarles la nueva capacidad.
2. Si algún flujo futuro depende de REST para Elementor o integraciones, deberá reabrirse con autenticación y autorización explícita.
3. El correo de soporte mostrado en acceso denegado usa el `admin_email` del sitio hasta que negocio defina un buzón oficial.
4. La validación ejecutable completa no pudo correrse desde este workspace porque el terminal actual no tiene `php` disponible en PATH.

---

## 13. Siguiente fase recomendada

1. Probar el flujo completo sobre la instalación WordPress local.
2. Auditar roles reales con WP-CLI o panel de usuarios.
3. Ajustar copies finales de `/ingresar/` y `/acceso-denegado/`.
4. Si negocio lo aprueba, agregar una pantalla administrativa simple para asignar la capacidad a roles custom sin tocar código.
