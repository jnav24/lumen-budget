FROM php:7.3-apache
RUN apt-get update
RUN apt-get install -y vim git zlib1g-dev default-mysql-client libzip-dev
RUN docker-php-ext-install mysqli pdo_mysql
RUN a2enmod rewrite
RUN sed -i 's!/var/www/html!/var/www/public!g' /etc/apache2/sites-available/000-default.conf
RUN mv /var/www/html /var/www/public
RUN curl -sS https://getcomposer.org/installer
RUN php -- --install-dir=/usr/local/bin --filename=composer
RUN echo "AllowEncodedSlashes On" >> /etc/apache2/apache2.conf
WORKDIR /var/www