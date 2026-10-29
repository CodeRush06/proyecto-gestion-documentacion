FROM php:8.2-apache

# RUN docker-php-ext-install mysqli
 RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /var/www/html

COPY . .

RUN a2enmod rewrite
