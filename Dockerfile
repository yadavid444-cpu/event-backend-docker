FROM php:8.3-cli

RUN apt-get update && apt-get install -y git unzip libzip-dev libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql bcmath zip gd \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN cp .env.example .env \
    && composer install --no-dev --optimize-autoloader --no-interaction \
    && php artisan key:generate --force \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8000

CMD ["sh", "-c", "for k in APP_URL APP_ENV APP_DEBUG DB_CONNECTION DB_HOST DB_PORT DB_DATABASE DB_USERNAME DB_PASSWORD BROADCAST_CONNECTION; do v=$(printenv $k); [ -n \"$v\" ] && sed -i \"s|^$k=.*|$k=$v|\" .env; done; until php artisan migrate --force; do echo waiting for database; sleep 3; done; php artisan storage:link --force; php artisan serve --host=0.0.0.0 --port=8000"]
