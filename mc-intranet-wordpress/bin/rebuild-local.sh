#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

WPCLI="docker compose --profile cli run --rm wpcli --allow-root"

wait_for_db() {
  local max_attempts=30
  local attempt=1

  until $WPCLI eval 'global $wpdb; exit( $wpdb->check_connection() ? 0 : 1 );' >/dev/null 2>&1; do
    if [ "$attempt" -ge "$max_attempts" ]; then
      echo "Error: la base de datos no responde despues de $max_attempts intentos." >&2
      exit 1
    fi

    echo "Esperando MySQL... intento $attempt/$max_attempts"
    attempt=$((attempt + 1))
    sleep 2
  done
}

echo "[1/6] Levantando servicios Docker..."
docker compose up -d --build

echo "[2/6] Esperando disponibilidad de MySQL..."
wait_for_db

echo "[3/6] Verificando instalacion de WordPress..."
if ! $WPCLI core is-installed >/dev/null 2>&1; then
  $WPCLI core install \
    --url="http://localhost:8000" \
    --title="MC Intranet" \
    --admin_user="admin" \
    --admin_password="Admin123!" \
    --admin_email="admin@local.test"
else
  echo "WordPress ya estaba instalado."
fi

echo "[4/6] Activando tema, plugin core y dependencias base..."
$WPCLI theme activate mc_intranet
$WPCLI plugin activate mc-intranet-core

for plugin in advanced-custom-fields elementor; do
  if ! $WPCLI plugin is-installed "$plugin" >/dev/null 2>&1; then
    $WPCLI plugin install "$plugin"
  fi
  $WPCLI plugin activate "$plugin"
done

echo "[5/6] Aplicando configuracion base..."
$WPCLI rewrite structure "/%postname%/" --hard
$WPCLI rewrite flush --hard

# WP-CLI corre en un contenedor separado sin Apache; en ese contexto --hard no
# siempre escribe reglas en .htaccess. Forzamos el bloque canonical de WP.
docker compose exec -T wordpress sh -lc 'cat > /var/www/html/.htaccess <<"EOF"
# BEGIN WordPress
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
RewriteBase /
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]
</IfModule>
# END WordPress
EOF
chown www-data:www-data /var/www/html/.htaccess
chmod 644 /var/www/html/.htaccess'

$WPCLI option update blogdescription "Portal interno MC"
$WPCLI option update timezone_string "America/Bogota"

if ! $WPCLI user get editor >/dev/null 2>&1; then
  $WPCLI user create editor editor@local.test --role=editor --user_pass="Editor123!"
else
  echo "Usuario editor ya existe."
fi

echo "[6/6] Ejecutando seed idempotente del contenido inicial..."
docker compose --profile cli run --rm wpcli sh -lc 'cd /var/www/html && sh wp-content/plugins/mc-intranet-core/bin/seed-content.sh'

echo ""
echo "Reconstruccion completada."
echo "- Sitio: http://localhost:8000"
echo "- Admin: http://localhost:8000/wp-admin"
echo "- Usuario admin: admin / Admin123!"
echo "- Usuario editor: editor / Editor123!"
