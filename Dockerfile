FROM php:8.2-apache

COPY . /var/www/html

# Update apt
RUN apt-get update
RUN apt-get install libcurl4 libcurl4-openssl-dev libzip-dev libpq-dev libpng-dev libfreetype6-dev libjpeg62-turbo-dev libxml2-dev -y

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Extensions
RUN docker-php-ext-install pcntl opcache soap zip pdo_pgsql pdo_mysql
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && docker-php-ext-install gd
RUN pecl install -o -f redis &&  rm -rf /tmp/pear \
    &&  docker-php-ext-enable redis

COPY docker/php.ini /usr/local/etc/php/conf.d/

# Apache
RUN a2enmod rewrite
COPY docker/000-default.conf /etc/apache2/sites-available/000-default.conf

# Xdebug
RUN yes | pecl install xdebug \
    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_autostart=off" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.mode=coverage" >> /usr/local/etc/php/conf.d/xdebug.ini 
    
# Remove dev composer packages
RUN composer install --optimize-autoloader --no-dev
RUN composer dump-autoload

EXPOSE 80
