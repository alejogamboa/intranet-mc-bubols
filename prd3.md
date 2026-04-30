# PRD - CTP para directorio corporativo de contactos

## Descripción general
El objetivo de este proyecto es desarrollar un CTP dentro del plugin MC Intranet Core que permita gestionar un directorio corporativo de contactos diferenciado por empresa (Company Context).

## Requisitos funcionales
1. Revisar implementación del custom post type mc_formulario y guiarse de su estructura para crear el nuevo CTP para el directorio de contactos.
2. El nuevo CTP se llamará `mc_directorio_contactos` y tendrá los siguientes campos personalizados:
   - Area
   - Cargo
   - Nombre
   - Celular
   - Email
3. crea metaboxes para cada uno de los campos personalizados mencionados anteriormente, permitiendo su edición desde el panel de administración de WordPress.
4. Implementar una función que permita importar los contactos desde un archivo CSV con la siguiente estructura:
  - Area;Cargo;Nombre;Celular;email;empresa(Company Context)

## short code
1. Crear un shortcode `[mc_directorio_contactos]` que permita mostrar el directorio de contactos en el frontend del sitio web, filtrando por empresa (Company Context) por ejemplo  `[mc_directorio_contactos empresa="essenza"]`
2. Insertar en cada pagina de cada empresa: /anstra/ essenza/ /budefry/ el shortcode con el filtro correspondiente para mostrar solo los contactos de esa empresa.

## Requisitos no funcionales
1. El CTP debe ser compatible con la versión actual de WordPress y seguir las mejores prácticas de desarrollo de plugins.
2. El código debe estar bien documentado y estructurado para facilitar su mantenimiento y futuras actualizaciones.
3. El plugin debe ser seguro y no introducir vulnerabilidades en el sitio web.
4. Usar los styles de MC Intranet Core para mantener la coherencia visual en el panel de administración.
5. El plugin debe ser probado exhaustivamente para asegurar su correcto funcionamiento antes de su lanzamiento.
6. Usar los skills que encuentres en este workspace como el de frontend y los skilss de wordpress para desarrollar el plugin de manera eficiente y efectiva.
