FROM php:7.4-apache
COPY src/ /var/www/html/
RUN echo "Servername localhost" >> /etc/apache2/apache2.conf
RUN docker-php-ext-install pdo pdo_mysql

EXPOSE 80