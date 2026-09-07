FROM node:22-alpine AS frontend

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY vite.config.js tsconfig.json ./
COPY resources ./resources

RUN npm run build

FROM php:8.4-cli-alpine

RUN apk add --no-cache icu-dev oniguruma-dev sqlite-dev \
    && docker-php-ext-install pdo_sqlite intl opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --no-progress --no-scripts --no-autoloader

COPY . .
COPY --from=frontend /app/public/build ./public/build

RUN composer dump-autoload --optimize --no-dev --classmap-authoritative \
    && mkdir -p database storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs \
    && chmod -R 777 storage database

ENV PORT=8000
ENV PHP_CLI_SERVER_WORKERS=4
ENV APP_ENV=production
ENV APP_DEBUG=false
ENV DB_CONNECTION=sqlite
ENV DB_DATABASE=/app/database/database.sqlite

EXPOSE 8000

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

CMD ["entrypoint.sh"]
