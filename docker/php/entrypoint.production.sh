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

# --- Guard: aplikasi ini WAJIB MySQL (migrasi pakai sintaks MySQL/ENUM) ---
# Tanpa ini Laravel jatuh ke default sqlite → migrasi gagal → 502.
if [ "${DB_CONNECTION:-sqlite}" != "mysql" ] || [ -z "$DB_HOST" ]; then
  echo "[entrypoint] FATAL: DB_CONNECTION harus 'mysql' dan DB_HOST harus terisi."
  echo "[entrypoint]   DB_CONNECTION='${DB_CONNECTION:-<kosong>}'  DB_HOST='${DB_HOST:-<kosong>}'"
  echo "[entrypoint]   Set variable DB_* di service web Railway (lihat README.deploy.md)."
  exit 1
fi

echo "[entrypoint] Menunggu MySQL siap..."
i=0
until php -r "try { new PDO('mysql:host='.getenv('DB_HOST').';port='.(getenv('DB_PORT')?:3306), getenv('DB_USERNAME'), getenv('DB_PASSWORD')); exit(0);} catch (Exception \$e) { exit(1);}" 2>/dev/null; do
  i=$((i+1))
  if [ "$i" -ge 30 ]; then
    echo "[entrypoint] FATAL: MySQL tidak bisa dihubungi setelah 60s. Cek DB_* & plugin MySQL."
    exit 1
  fi
  echo "[entrypoint] MySQL belum siap, mencoba lagi..."
  sleep 2
done
echo "[entrypoint] MySQL siap."

echo "[entrypoint] Menjalankan migrasi..."
php artisan migrate --force

php artisan storage:link --force 2>/dev/null || true

echo "[entrypoint] Optimasi cache produksi..."
php artisan optimize:clear || true
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

chown -R www-data:www-data storage bootstrap/cache || true

echo "[entrypoint] Inisialisasi selesai. Menjalankan supervisor..."
exec "$@"
