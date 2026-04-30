# Comandos WP-CLI para MC Intranet

Esta guia contiene comandos utiles para ejecutar WP-CLI usando Docker Compose en este proyecto.

## 0. Reconstruccion completa (recomendado tras perdida de Docker)

```bash
cd /Users/alejandrogamboa/Documents/bubols-fryscol/intranet/mc-intranet-wordpress
bash bin/rebuild-local.sh
```

Este script ejecuta todo el flujo base de forma idempotente: levantar stack, instalar WordPress (si aplica), activar tema/plugins y cargar seed.

## 1. Levantar contenedores

```bash
cd /Users/alejandrogamboa/Documents/bubols-fryscol/intranet/mc-intranet-wordpress
docker compose up -d
```

## 2. Verificar conexion WP-CLI

```bash
docker compose --profile cli run --rm wpcli core version
docker compose --profile cli run --rm wpcli db check
```

## 3. Instalar WordPress (solo primera vez)

```bash
docker compose --profile cli run --rm wpcli core install \
  --url=http://localhost:8000 \
  --title="MC Intranet" \
  --admin_user=admin \
  --admin_password=Admin123! \
  --admin_email=admin@local.test
```

## 4. Activar el tema

```bash
docker compose --profile cli run --rm wpcli theme activate mc_intranet
docker compose --profile cli run --rm wpcli theme list
```

## 5. Crear un usuario editor

```bash
docker compose --profile cli run --rm wpcli user create editor editor@local.test --role=editor --user_pass=Editor123!
```

## 6. Ajustes recomendados

```bash
docker compose --profile cli run --rm wpcli rewrite structure "/%postname%/" --hard
docker compose --profile cli run --rm wpcli rewrite flush --hard
docker compose --profile cli run --rm wpcli option update blogdescription "Portal interno MC"
```

## 7. Plugins (ejemplos)

```bash
docker compose --profile cli run --rm wpcli plugin list
docker compose --profile cli run --rm wpcli plugin install query-monitor --activate
```

## 8. Seed de datos iniciales (plugin core)

```bash
docker compose --profile cli run --rm wpcli bash -lc 'cd /var/www/html && bash wp-content/plugins/mc-intranet-core/bin/seed-content.sh'
```

Notas:
- El seed es idempotente para formularios, sedes y páginas por slug canónico.
- Si necesitas relanzarlo, puedes ejecutar el mismo comando sin duplicar registros canonizados.
