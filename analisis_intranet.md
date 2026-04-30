# Análisis a Profundidad: MC - Intranet Corporativa

**URL Analizada:** [https://sites.google.com/anstra.com.co/mc-intranet/](https://sites.google.com/anstra.com.co/mc-intranet/)

Este documento detalla la estructura, organización de contenidos y puntos de interacción de la Intranet Corporativa. Ha sido diseñado como insumo para el entrenamiento o configuración de un agente de Inteligencia Artificial enfocado en automatización, consulta de información y gestión de procesos corporativos.

---

## 1. Visión General del Sitio

La intranet es un portal centralizado, aparentemente desarrollado en **Google Sites**. Su propósito principal es unificar la información, procesos y recursos para los colaboradores de un grupo corporativo que aglomera a tres compañías distintas:
- **Projection Anstra**: Gestión administrativa, contabilidad y RR. HH.
- **Essenza Foods (NIT 901 971 854-7)**: Gestión de marca, comercial, mercadeo y ventas.
- **Budefry SAS (NIT 901 565 887-9)**: Operación y procesos de producción.

Todas comparten un núcleo administrativo (NIT 901 967 530-0).

---

## 2. Estructura de Navegación y Secciones

El sitio se divide en 5 pestañas principales:

### A. Inicio (Multicompañía)
Centraliza procesos transversales a todo el grupo. Se divide en tres áreas clave:
- **ADMINISTRACIÓN:**
  - [Solicitud de Tiquetes Aéreos y Terrestre](https://forms.gle/P5G3SVjKKtoYno5A8)
  - [Solicitud de Viáticos](https://forms.gle/iwwYKaHEe9Ns8avP7)
  - [Solicitud Reserva de Hospedaje](https://forms.gle/ePwLxsPUGVQNxasM6)
- **TIC (Tecnologías de la Información y Comunicaciones):**
  - [Soporte TIC](https://forms.gle/Jan3qP5n4zK1CkEK8)
  - [Gestión de Usuarios TIC](https://forms.gle/zgyn7vaouK9ttLKp8)
  - [Compras TIC](https://forms.gle/Vg93Fqsb4r7ajzsZ9)
- **GESTIONES:**
  - [Solicitud de Servicio Logístico (No Venta)](https://forms.gle/KkM9QLUoq9raV7UE9)
  - [Registro Certificados de Votación](https://forms.gle/rJcfzBHREexeLEpx5)

### B. Páginas por Compañía (Projection Anstra, Essenza Foods, Budefry)
Cada empresa cuenta con su propio portal para trámites específicos (predominantemente de Recursos Humanos). La estructura es idéntica en las tres, pero **cada botón dirige a un enlace (Formulario de Google / Documento) único y diferente según la empresa**.

*Estructura de la sección **RECURSOS HUMANOS** para cada empresa:*
1. **Perfil Sociodemográfico** (Google Forms).
2. **Solicitud de Certificado Laboral** (Google Forms) -> *Destacado como un ejemplo de gestión en la página de inicio.*
3. **Formato Solicitud de Mejora** (Documentos de Google en formato DOCX descargable).
4. **Solicitud de Inicio de Proceso Disciplinario** (Google Forms).
5. **Directorio Corporativo** (Mencionado como sección, probablemente integrado o estático).

### C. Interactúa
Pestaña dedicada al *Employer Branding* institucional y comunicación interna.
- **Reconocimientos Corporativos**: Publicación de logros académicos y profesionales de los colaboradores.
- **Eventos Importantes**: Avisos generales y celebraciones.

### D. Footer / Información de Ubicación
En el pie de todas las subpáginas se desglosan las sedes del grupo, las cuales están hipervinculadas a **Google Maps**:
- **Administrativa:** Calle 49 # 77A - 19, Barrio Laureles (Estadio), Medellín (Ant.)
- **Comercial (Essenza Foods):** Carrera 74 # 48B - 59, Barrio Laureles (Estadio), Medellín (Ant.)
- **Producción (Budefry):** Parque Industrial Rosendal Bod 18, Km 24 Vda La Clara-Guarne (Ant.)

---

## 3. Oportunidades y Tareas para un Agente de Inteligencia Artificial

A partir del análisis de la Intranet, las siguientes tareas o flujos son los candidatos ideales para ser delegados, automatizados o asistidos por un agente de IA:

### I. Asistente de Navegación y Preguntas Frecuentes (Bot/Copilot)
- **Enrutamiento de Solicitudes:** Identificar de qué empresa es el usuario y redirigirlo al formulario correcto. *(Por ejemplo: "Necesito un certificado laboral". La IA pregunta: "¿A qué empresa perteneces? Projection, Essenza o Budefry" y retorna el link exacto).*
- **Resolución de dudas logísticas:** Entregar la dirección de sede y link a Google Maps según la empresa buscada.

### II. Automatización de Envío y Clasificación de Formularios
*Dado que la intranet depende intensamente de Google Forms, un Agente IA con integración a las APIs de Google Workspace podría:*
- **Viáticos y Viajes (Administración):** Procesar y aprobar automáticamente pre-solicitudes utilizando lineamientos de viáticos (NLP para leer los campos enviados).
- **Servicio Logístico & TIC:** Actuar como primer nivel de la Mesa de Ayuda (Help Desk). El agente puede analizar la "Solicitud de Soporte TIC", intentar proveer la solución basada en una base de conocimientos, o escalar el ticket.

### III. Generación y Pre-llenado de Documentos
- **RRHH - Proceso Disciplinario y Certificados:** Recibir la intención del usuario a través de un chat y rellenar automáticamente el Formulario de Google en representación del usuario, haciendo preguntas secuenciales (Nombre, Cédula, Razón del proceso, etc.).
- **Formatos de Mejora:** Dado que el "Formato solicitud de mejora" es un DOCX subido a GDrive, el agente IA podría estructurar la idea de mejora dada por el usuario en texto libre y completar o asistir en la redacción de este documento.

### IV. Recuperación de Información de RRHH y Cultura
- Conectar al **Directorio Corporativo** para buscar contactos rápidamente (ej. "IA, dame el número del jefe comercial de Essenza Foods").
- Scraping o lectura de la pestaña **Interactúa** para mantener resúmenes semanales de novedades institucionales que puede enviar por correo o Slack/Teams.

---
**Conclusión Técnica:** La intranet está enfocada en el enrutamiento más que en el alojamiento directo de sistemas nativos; actúa como un concentrador de enlaces de Google Workspace (Forms, Docs y Maps). El componente fundamental para el Agente IA radicará en la correcta comprensión del contexto multicompañía (a qué filial pertenece la gestión) y la interacción mediante APIs de Google Forms y Google Drive.
