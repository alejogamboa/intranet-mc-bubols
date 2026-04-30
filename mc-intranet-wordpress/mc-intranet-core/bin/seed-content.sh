#!/usr/bin/env bash
# =============================================================================
# seed-content.sh — Carga inicial de contenido para MC Intranet
# =============================================================================
# Uso: ejecutar DENTRO del contenedor wpcli
#   docker compose --profile cli run --rm wpcli bash /var/www/html/wp-content/plugins/mc-intranet-core/bin/seed-content.sh
#
# O desde el host:
#   docker compose --profile cli run --rm wpcli bash -c "cd /var/www/html && bash wp-content/plugins/mc-intranet-core/bin/seed-content.sh"
#
# Requiere: WordPress instalado, tema mc_intranet activo, plugin mc-intranet-core activo.
# =============================================================================

set -euo pipefail

WP="wp --allow-root"

slugify() {
    local value="$1"
    # shellcheck disable=SC2001
    value=$(printf '%s' "$value" | iconv -t ascii//TRANSLIT 2>/dev/null || printf '%s' "$value")
    value=$(printf '%s' "$value" | tr '[:upper:]' '[:lower:]')
    value=$(printf '%s' "$value" | sed -E 's/[^a-z0-9]+/-/g; s/^-+//; s/-+$//')
    printf '%s' "$value"
}

echo "=== MC Intranet — Carga inicial de contenido ==="

# ─── 1. Activar tema y plugin ─────────────────────────────────────────────────
echo "[1/7] Activando tema y plugin..."
$WP theme activate mc_intranet
$WP plugin activate mc-intranet-core

# ─── 2. Crear términos de taxonomías ─────────────────────────────────────────
echo "[2/7] Creando términos de taxonomías..."

# Empresas
for slug in mc anstra essenza budefry interactua; do
    $WP term create mc_empresa "$slug" --slug="$slug" --skip-dup 2>/dev/null || true
done

# Áreas
for slug in administracion tic gestiones rrhh cultura; do
    $WP term create mc_area "$slug" --slug="$slug" --skip-dup 2>/dev/null || true
done

echo "  Taxonomías listas."

# ─── 3. Crear formularios transversales (empresa=mc) ─────────────────────────
echo "[3/7] Creando formularios transversales..."

create_formulario() {
    local title="$1"
    local empresa="$2"
    local area="$3"
    local form_url="$4"
    local form_type="${5:-form}"
    local order_weight="${6:-50}"
    local excerpt="$7"

    local base_slug
    local post_slug
    local post_id

    base_slug=$(slugify "$title")
    post_slug="form-${empresa}-${area}-${base_slug}"

    post_id=$($WP post list \
        --post_type=mc_formulario \
        --name="$post_slug" \
        --field=ID \
        --format=ids 2>/dev/null || true)

    if [ -n "$post_id" ]; then
        $WP post update "$post_id" \
            --post_title="$title" \
            --post_excerpt="$excerpt" \
            --post_status=publish >/dev/null
        echo "  ~ [$post_id] $title (actualizado)"
    else
        post_id=$($WP post create \
            --post_type=mc_formulario \
            --post_title="$title" \
            --post_name="$post_slug" \
            --post_excerpt="$excerpt" \
            --post_status=publish \
            --porcelain)
        echo "  + [$post_id] $title"
    fi

    $WP post term set "$post_id" mc_empresa "$empresa"
    $WP post term set "$post_id" mc_area    "$area"
    $WP post meta update "$post_id" company_context "$empresa"
    $WP post meta update "$post_id" area_context    "$area"
    $WP post meta update "$post_id" form_type       "$form_type"
    $WP post meta update "$post_id" form_url        "$form_url"
    $WP post meta update "$post_id" cta_label       "Abrir formulario"
    $WP post meta update "$post_id" open_new_tab    "1"
    $WP post meta update "$post_id" is_featured     "0"
    $WP post meta update "$post_id" order_weight    "$order_weight"

}

# Administración
create_formulario \
    "Solicitud de Tiquetes Aéreos y Terrestres" \
    "mc" "administracion" \
    "https://forms.gle/P5G3SVjKKtoYno5A8" \
    "form" "10" \
    "Gestiona tus tiquetes aéreos y terrestres para desplazamientos corporativos nacionales."

create_formulario \
    "Solicitud de Viáticos" \
    "mc" "administracion" \
    "https://forms.gle/iwwYKaHEe9Ns8avP7" \
    "form" "20" \
    "Solicita viáticos para alimentación, transporte y gastos autorizados en comisiones."

create_formulario \
    "Solicitud de Reserva de Hospedaje" \
    "mc" "administracion" \
    "https://forms.gle/ePwLxsPUGVQNxasM6" \
    "form" "30" \
    "Solicita la reserva de alojamiento para viajes y comisiones autorizadas."

# TIC
create_formulario \
    "Soporte TIC" \
    "mc" "tic" \
    "https://forms.gle/Jan3qP5n4zK1CkEK8" \
    "form" "10" \
    "Reporta incidentes técnicos, fallas de hardware o software a la mesa de ayuda corporativa."

create_formulario \
    "Gestión de Usuarios TIC" \
    "mc" "tic" \
    "https://forms.gle/zgyn7vaouK9ttLKp8" \
    "form" "20" \
    "Solicita creación, modificación o baja de usuarios en los sistemas corporativos."

create_formulario \
    "Compras TIC" \
    "mc" "tic" \
    "https://forms.gle/Vg93Fqsb4r7ajzsZ9" \
    "form" "30" \
    "Solicita la adquisición de equipos, licencias y accesorios tecnológicos."

# Gestiones
create_formulario \
    "Solicitud de Servicio Logístico (No Venta)" \
    "mc" "gestiones" \
    "https://forms.gle/KkM9QLUoq9raV7UE9" \
    "form" "10" \
    "Gestiona servicios de logística interna no relacionados con procesos comerciales de venta."

create_formulario \
    "Registro de Certificados de Votación" \
    "mc" "gestiones" \
    "https://forms.gle/rJcfzBHREexeLEpx5" \
    "form" "20" \
    "Registra tu certificado de votación para cumplir con el requisito electoral corporativo."

# ─── 4. Crear formularios RRHH por empresa (pendientes) ──────────────────────
echo "[4/7] Creando formularios RRHH por empresa..."

for empresa in anstra essenza budefry; do

    case "$empresa" in
        anstra)   empresa_label="Projection Anstra" ;;
        essenza)  empresa_label="Essenza Foods" ;;
        budefry)  empresa_label="Budefry SAS" ;;
    esac

    create_formulario \
        "Perfil Sociodemográfico — $empresa_label" \
        "$empresa" "rrhh" \
        "" "form" "10" \
        "Actualiza tu información personal y socioeconómica en el sistema de RRHH."

    create_formulario \
        "Certificado Laboral — $empresa_label" \
        "$empresa" "rrhh" \
        "" "form" "20" \
        "Solicita tu certificado laboral para trámites externos."

    create_formulario \
        "Formato Solicitud de Mejora — $empresa_label" \
        "$empresa" "rrhh" \
        "" "doc" "30" \
        "Descarga el formato DOCX para radicar solicitudes de mejora de procesos."

    create_formulario \
        "Inicio Proceso Disciplinario — $empresa_label" \
        "$empresa" "rrhh" \
        "" "form" "40" \
        "Formulario para iniciar un proceso disciplinario según el reglamento interno."

    create_formulario \
        "Directorio Corporativo — $empresa_label" \
        "$empresa" "rrhh" \
        "" "doc" "50" \
        "Accede al directorio de contactos corporativos de la empresa."
done

# ─── 5. Crear sedes ───────────────────────────────────────────────────────────
echo "[5/7] Creando sedes..."

create_sede() {
    local title="$1"
    local company_label="$2"
    local address="$3"
    local maps_url="$4"
    local menu_order="$5"

    local post_slug
    local post_id

    post_slug="sede-$(slugify "$title")"

    post_id=$($WP post list \
        --post_type=mc_sede \
        --name="$post_slug" \
        --field=ID \
        --format=ids 2>/dev/null || true)

    if [ -n "$post_id" ]; then
        $WP post update "$post_id" \
            --post_title="$title" \
            --menu_order="$menu_order" \
            --post_status=publish >/dev/null
        echo "  ~ [$post_id] $title (actualizada)"
    else
        post_id=$($WP post create \
            --post_type=mc_sede \
            --post_title="$title" \
            --post_name="$post_slug" \
            --post_status=publish \
            --menu_order="$menu_order" \
            --porcelain)
        echo "  + [$post_id] $title"
    fi

    $WP post meta update "$post_id" company_label "$company_label"
    $WP post meta update "$post_id" address_full  "$address"
    $WP post meta update "$post_id" maps_url      "$maps_url"
}

create_sede \
    "Sede Administrativa" \
    "Administrativa" \
    "Calle 49 # 77A - 19, Barrio Laureles (Estadio), Medellín, Antioquia" \
    "https://maps.google.com" \
    "1"

create_sede \
    "Sede Comercial Essenza" \
    "Essenza Foods" \
    "Carrera 74 # 48B - 59, Barrio Laureles (Estadio), Medellín, Antioquia" \
    "https://maps.google.com" \
    "2"

create_sede \
    "Sede Producción Budefry" \
    "Budefry SAS" \
    "Vía Guarne km 2, Guarne, Antioquia" \
    "https://maps.google.com" \
    "3"

# ─── 6. Crear páginas de empresa ─────────────────────────────────────────────
echo "[6/7] Creando páginas de empresa..."

create_company_page() {
    local title="$1"
    local slug="$2"
    local company="$3"
    local content="$4"

    local post_id
    post_id=$($WP post list \
        --post_type=page \
        --name="$slug" \
        --field=ID \
        --format=ids 2>/dev/null || true)

    if [ -n "$post_id" ]; then
        $WP post update "$post_id" \
            --post_title="$title" \
            --post_content="$content" \
            --post_status=publish >/dev/null
        echo "  ~ [$post_id] $title (actualizada)"
    else
        post_id=$($WP post create \
            --post_type=page \
            --post_title="$title" \
            --post_name="$slug" \
            --post_content="$content" \
            --post_status=publish \
            --porcelain)
        echo "  + [$post_id] $title (slug: $slug)"
    fi

    $WP post meta update "$post_id" company_context "$company"
}

create_company_page \
    "Projection Anstra" "anstra" "anstra" \
    '[mc_context_alert empresa="anstra"]
<section class="section-block"><div class="container"><div class="section-header"><div class="section-header__text"><p class="section-header__label">Recursos Humanos</p><h2 class="section-header__title">RRHH — Projection Anstra</h2><p class="section-header__desc">Formularios y documentos exclusivos de Projection Anstra.</p></div></div>[mc_formularios empresa="anstra" area="rrhh"]</div></section>
<section class="section-block"><div class="container"><div class="section-header"><div class="section-header__text"><p class="section-header__label">Directorio Corporativo</p><h2 class="section-header__title">Contactos — Projection Anstra</h2><p class="section-header__desc">Directorio de contactos corporativos de Projection Anstra.</p></div></div>[mc_directorio_contactos empresa="anstra"]</div></section>
[mc_company_portals]'

create_company_page \
    "Essenza Foods" "essenza" "essenza" \
    '[mc_context_alert empresa="essenza"]
<section class="section-block"><div class="container"><div class="section-header"><div class="section-header__text"><p class="section-header__label">Recursos Humanos</p><h2 class="section-header__title">RRHH — Essenza Foods</h2><p class="section-header__desc">Formularios y documentos exclusivos de Essenza Foods.</p></div></div>[mc_formularios empresa="essenza" area="rrhh"]</div></section>
<section class="section-block"><div class="container"><div class="section-header"><div class="section-header__text"><p class="section-header__label">Directorio Corporativo</p><h2 class="section-header__title">Contactos — Essenza Foods</h2><p class="section-header__desc">Directorio de contactos corporativos de Essenza Foods.</p></div></div>[mc_directorio_contactos empresa="essenza"]</div></section>
[mc_company_portals]'

create_company_page \
    "Budefry" "budefry" "budefry" \
    '[mc_context_alert empresa="budefry"]
<section class="section-block"><div class="container"><div class="section-header"><div class="section-header__text"><p class="section-header__label">Recursos Humanos</p><h2 class="section-header__title">RRHH — Budefry SAS</h2><p class="section-header__desc">Formularios y documentos exclusivos de Budefry SAS.</p></div></div>[mc_formularios empresa="budefry" area="rrhh"]</div></section>
<section class="section-block"><div class="container"><div class="section-header"><div class="section-header__text"><p class="section-header__label">Directorio Corporativo</p><h2 class="section-header__title">Contactos — Budefry SAS</h2><p class="section-header__desc">Directorio de contactos corporativos de Budefry SAS.</p></div></div>[mc_directorio_contactos empresa="budefry"]</div></section>
[mc_company_portals]'

create_company_page \
    "Interactúa" "interactua" "interactua" \
    '<section class="section-block"><div class="container"><div class="section-header"><div class="section-header__text"><p class="section-header__label">Cultura Corporativa</p><h2 class="section-header__title">Reconocimientos</h2><p class="section-header__desc">Colaboradores que hacen grande al grupo MC.</p></div></div>[mc_reconocimientos limit="12"]</div></section>
<section class="section-block"><div class="container"><div class="section-header"><div class="section-header__text"><p class="section-header__label">Cultura Corporativa</p><h2 class="section-header__title">Eventos Importantes</h2><p class="section-header__desc">Calendario de eventos del grupo corporativo.</p></div></div>[mc_eventos limit="10"]</div></section>'

# ─── 7. Importar directorio de contactos ────────────────────────────────────
echo "[7/7] Importando directorio de contactos..."

CSV_SRC="/var/www/html/wp-content/plugins/mc-intranet-core/bin/directorio-mc-intranet.csv"

if [ -f "$CSV_SRC" ]; then
    import_contact() {
        local area="$1"
        local cargo="$2"
        local nombre="$3"
        local celular="$4"
        local email="$5"
        local empresa="$6"

        local post_slug
        post_slug="contacto-$(slugify "$nombre")-$(slugify "$empresa")"

        # Buscar por slug canónico o por metas nombre+empresa
        local post_id
        post_id=$($WP post list \
            --post_type=mc_directorio \
            $WP post update "$post_id" \
                --post_title="$nombre" \
                --post_status=publish >/dev/null
        else
            post_id=$($WP post create \
                --post_type=mc_directorio \
                --post_title="$nombre" \
                --post_name="$post_slug" \
                --post_status=publish \
                --porcelain)
            echo "  + [$post_id] $nombre ($empresa)"
        fi

        $WP post meta update "$post_id" area            "$area"
        $WP post meta update "$post_id" cargo           "$cargo"
        $WP post meta update "$post_id" nombre          "$nombre"
        $WP post meta update "$post_id" celular         "$celular"
        $WP post meta update "$post_id" email           "$email"
        $WP post meta update "$post_id" company_context "$empresa"
    }

    # Leer CSV línea por línea (saltar cabecera)
    first_line=true
    while IFS=';' read -r area cargo nombre celular email empresa; do
        if $first_line; then
            first_line=false
            continue
        fi
        # Limpiar espacios y CR
        area=$(printf '%s' "$area" | tr -d '\r' | sed 's/^ *//;s/ *$//')
        cargo=$(printf '%s' "$cargo" | tr -d '\r' | sed 's/^ *//;s/ *$//')
        nombre=$(printf '%s' "$nombre" | tr -d '\r' | sed 's/^ *//;s/ *$//')
        celular=$(printf '%s' "$celular" | tr -d '\r' | sed 's/^ *//;s/ *$//')
        email=$(printf '%s' "$email" | tr -d '\r' | sed 's/^ *//;s/ *$//')
        empresa=$(printf '%s' "$empresa" | tr -d '\r' | sed 's/^ *//;s/ *$//')

        [ -z "$nombre" ] && continue
        [ -z "$empresa" ] && continue

        import_contact "$area" "$cargo" "$nombre" "$celular" "$email" "$empresa"
    done < "$CSV_SRC"

    echo "  Directorio de contactos importado."
else
    echo "  [!] CSV no encontrado en $CSV_SRC — saltando importación."
    echo "      Puedes importarlo manualmente desde el panel de administración:"
    echo "      WP Admin > Directorio Contactos > Importar CSV"
fi

# ─── Configuración de front-page ──────────────────────────────────────────────
echo ""
echo "[Extra] Configurando página de inicio..."

# Verificar si ya existe una página de inicio, si no crear una
FRONT_ID=$($WP post list --post_type=page --name=inicio --field=ID --format=ids 2>/dev/null || echo "")
if [ -z "$FRONT_ID" ]; then
    FRONT_ID=$($WP post create \
        --post_type=page \
        --post_title="Inicio" \
        --post_name="inicio" \
        --post_status=publish \
        --porcelain)
    echo "  + [$FRONT_ID] Página Inicio creada."
fi

$WP option update show_on_front  page
$WP option update page_on_front  "$FRONT_ID"

$WP rewrite flush --hard

echo ""
echo "=== Seed completado exitosamente ==="
echo ""
echo "URLs de verificación:"
echo "  Inicio:             http://localhost:8000/"
echo "  Projection Anstra:  http://localhost:8000/anstra/"
echo "  Essenza Foods:      http://localhost:8000/essenza/"
echo "  Budefry:            http://localhost:8000/budefry/"
echo "  Interactúa:         http://localhost:8000/interactua/"
echo ""
echo "Recuerda: actualizar las URLs de RRHH cuando estén disponibles."
