FROM php:7.4-apache

COPY ./docker/config/bcmath.ini /etc/php/7.4/cli/conf.d/20-bcmath.ini
COPY ./docker/config/php.ini /usr/local/etc/php/php.ini
COPY ./src /var/www/html/
RUN echo "Servername localhost" >> /etc/apache2/apache2.conf
RUN docker-php-ext-install pdo pdo_mysql
USER 1000:1000

WORKDIR /var/www/html