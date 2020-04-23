FROM php:7.2-fpm

RUN apt-get update && apt-get install -y zlib1g-dev libicu-dev g++ \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl

RUN pecl install -o -f grpc \
    &&  rm -rf /tmp/pear \
    &&  docker-php-ext-enable grpc

COPY ./ /var/www/html/

RUN chmod -R 0777 /var/www/html/tmp /var/www/html/logs
