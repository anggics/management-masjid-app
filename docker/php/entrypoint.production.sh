#!/bin/sh
set -e

cd /var/www

# --- Render konfigurasi nginx dengan $PORT (Railway inject port dinamis) ---
: "${PORT:=8080}"
echo "[entrypoint] Render nginx config untuk PORT=$PORT..."
# Render ke file template terpisah lalu tulis sebagai SATU-SATUNYA server config.
# Hapus default bawaan Alpine agar tidak ada server lain yang listen di :80.
rm -f /etc/nginx/http.d/*.conf
envsubst '${PORT}' < /etc/nginx/templates/default.conf.template > /etc/nginx/http.d/default.conf
echo "[entrypoint] nginx akan listen di: $(grep -m1 listen /etc/nginx/http.d/default.conf | tr -s ' ')"

# --- APP_KEY: di Railway diisi via env var. Jika kosong, generate sementara
#     (in-memory) agar aplikasi tidak crash. Disarankan set APP_KEY tetap di
#     Environment Variables Railway agar session/enkripsi konsisten. ---
if [ -z "$APP_KEY" ]; then
  echo "[entrypoint] WARN: APP_KEY kosong. Generate sementara — set APP_KEY di Railway!"
  export APP_KEY="$(php artisan key:generate --show)"
fi

# --- Tunggu MySQL siap (Railway plugin DB) ---
if [ -n "$DB_HOST" ]; then
  echo "[entrypoint] Menunggu MySQL siap..."
  i=0
  until php -r "try { new PDO('mysql:host='.getenv('DB_HOST').';port='.(getenv('DB_PORT')?:3306), getenv('DB_USERNAME'), getenv('DB_PASSWORD')); exit(0);} catch (Exception \$e) { exit(1);}" 2>/dev/null; do
    i=$((i+1))
    if [ "$i" -ge 30 ]; then
      echo "[entrypoint] WARN: MySQL belum siap setelah 60s, lanjut tetap jalan."
      break
    fi
    echo "[entrypoint] MySQL belum siap, mencoba lagi..."
    sleep 2
  done
fi

echo "[entrypoint] Menjalankan migrasi..."
php artisan migrate --force || echo "[entrypoint] WARN: migrate gagal."

php artisan storage:link 2>/dev/null || true

echo "[entrypoint] Optimasi cache produksi..."
php artisan optimize:clear || true
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

chown -R www-data:www-data storage bootstrap/cache || true

echo "[entrypoint] Inisialisasi selesai. Menjalankan supervisor..."
exec "$@"
