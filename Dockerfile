FROM composer:2 AS dependencies
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

FROM php:8.2-cli
RUN docker-php-ext-install pdo_sqlite
WORKDIR /app
COPY . .
COPY --from=dependencies /app/vendor ./vendor
RUN mkdir -p storage/framework/{cache,sessions,views} storage/logs database \
    && touch database/database.sqlite \
    && chmod -R u+rwX storage bootstrap/cache database
EXPOSE 8000
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
