FROM dfossacecchi/php-fpm:v8.2-1.1

ARG UID
ARG GID

RUN apk --update --no-cache add \
    postgresql-dev \
    $PHPIZE_DEPS

RUN pecl install redis
RUN docker-php-ext-install pdo_pgsql
RUN docker-php-ext-enable redis

RUN apk --no-cache add shadow && usermod -u $UID www-data && groupmod -g $GID www-data
RUN chown -R www-data:www-data /var/www
RUN chown -R www-data:www-data /home/www-data

WORKDIR /var/www
