FROM php:7.4-apache

COPY ./src /var/www/html/
COPY ./docker/config/php.ini /usr/local/etc/php/php.ini

RUN echo "Servername localhost" >> /etc/apache2/apache2.conf
RUN docker-php-ext-install pdo pdo_mysql
USER 1000:1000

WORKDIR /var/www/html