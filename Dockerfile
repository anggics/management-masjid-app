# ---------------------------------------------------------------------------
# Aplikasi PWA Masjid & Mushola — Laravel 11 (PHP 8.2 FPM)
# Skeleton Laravel dibuat saat build, lalu di-overlay dengan kode aplikasi
# kustom dari ./src. Tidak perlu PHP/Composer terinstall di host.
# ---------------------------------------------------------------------------
FROM php:8.2-fpm-alpine

# --- System & PHP extensions ---
RUN apk add --no-cache \
        git curl bash zip unzip \
        libpng-dev libjpeg-turbo-dev freetype-dev \
        oniguruma-dev icu-dev libzip-dev \
        nodejs npm mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo pdo_mysql gd exif opcache bcmath intl zip pcntl mbstring \
    && apk add --no-cache --virtual .build-deps autoconf g++ make \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

# --- Composer (pin 2.7: sebelum fitur advisory-blocking yang menolak install laravel/framework) ---
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1
WORKDIR /var/www

# 1) Buat skeleton Laravel 11 bersih.
#    Dibuat di folder sementara karena /var/www sudah berisi "html" dari base image
#    (composer create-project menolak direktori yang tidak kosong).
RUN rm -rf /var/www/html \
    && composer create-project "laravel/laravel:^11.0" /usr/src/laravel --no-interaction --prefer-dist \
    && cp -a /usr/src/laravel/. /var/www/ \
    && rm -rf /usr/src/laravel

# 2) Paket tambahan sesuai System Design
RUN composer require laravel/sanctum barryvdh/laravel-dompdf --no-interaction \
    && php artisan install:api --no-interaction || true

# 3) Overlay kode aplikasi kustom (menimpa file skeleton)
COPY src/ /var/www/

# 4) Dependensi & build aset frontend (Tailwind + Alpine + Vite)
#    Tanpa --optimize (classmap statis) agar class baru yang ditambahkan via
#    bind mount ./src tetap terbaca PSR-4 saat runtime tanpa perlu rebuild.
RUN composer dump-autoload \
    && npm install \
    && npm run build

# 4b) Simpan salinan folder public/ (index.php, build, dst) di luar /var/www.
#     public/ dipakai bersama container app & nginx lewat named volume app_public;
#     entrypoint menyalin isi ini ke volume saat start agar index.php & aset Vite
#     terbaru selalu tersedia (vendor & artisan tetap utuh dari image karena tidak
#     di-bind-mount).
RUN mkdir -p /opt/app-build \
    && cp -a /var/www/public /opt/app-build/public

# 5) Permission & entrypoint
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
COPY docker/php/php.ini /usr/local/etc/php/conf.d/zz-masjid.ini
RUN chmod +x /usr/local/bin/entrypoint.sh \
    && chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000
ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
