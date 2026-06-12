#!/bin/sh
set -e

cd /var/www

# --- Sinkronisasi public/ ke named volume app_public (dev bind-mount) ---
# Folder kode (app, config, dst) di-bind-mount dari ./src, tapi public/ berisi
# index.php & aset Vite (public/build) yang dibuat di image dan TIDAK ada di host.
# Salin dari salinan image (/opt/app-build/public) ke volume app_public agar
# index.php & aset terbaru selalu tersedia. (vendor & artisan tetap dari image.)
if [ -d /opt/app-build/public ]; then
  echo "[entrypoint] Menyinkronkan public/ dari image ke volume..."
  cp -a /opt/app-build/public/. /var/www/public/ || echo "[entrypoint] WARN: gagal menyalin public/"
fi

echo "[entrypoint] Menunggu MySQL siap..."
until php -r "try { new PDO('mysql:host='.getenv('DB_HOST').';port='.getenv('DB_PORT'), getenv('DB_USERNAME'), getenv('DB_PASSWORD')); exit(0);} catch (Exception \$e) { exit(1);} " 2>/dev/null; do
  echo "[entrypoint] MySQL belum siap, mencoba lagi..."
  sleep 2
done
echo "[entrypoint] MySQL siap."

# Hanya container 'app' yang menjalankan inisialisasi (migrasi, seed, cache).
if [ "$CONTAINER_ROLE" = "app" ]; then
  # APP_KEY
  if ! grep -q "^APP_KEY=base64:" .env 2>/dev/null; then
    echo "[entrypoint] Generate APP_KEY..."
    php artisan key:generate --force
  fi

  echo "[entrypoint] Menjalankan migrasi..."
  php artisan migrate --force

  php artisan storage:link || true

  echo "[entrypoint] Membersihkan cache lama..."
  php artisan optimize:clear || true

  echo "[entrypoint] Optimasi cache..."
  php artisan config:cache || true
  php artisan route:cache || true
  php artisan view:cache || true

  chown -R www-data:www-data storage bootstrap/cache || true

  touch /tmp/app-ready
  echo "[entrypoint] Inisialisasi selesai. Aplikasi siap."
fi

exec "$@"
