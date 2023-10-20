FROM php:8.2-fpm-alpine

ARG UID
ARG GID

RUN apk --update --no-cache add \
    postgresql-dev \
    libzip-dev \
    zip \
    unzip \
    libpng \
    libpng-dev \
    libjpeg-turbo \
    libjpeg-turbo-dev \
    freetype \
    freetype-dev \
    oniguruma-dev \
    pcre-dev \
    $PHPIZE_DEPS

RUN pecl install redis
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-configure intl
RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd zip intl
RUN docker-php-ext-enable redis

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN apk --no-cache add shadow && usermod -u $UID www-data && groupmod -g $GID www-data
RUN chown -R www-data:www-data /var/www
RUN chown -R www-data:www-data /home/www-data

WORKDIR /var/www
