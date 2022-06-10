FROM php:8.1.5-fpm-alpine3.15

WORKDIR /var/www/html

#install GD
RUN apk add --no-cache freetype libpng libjpeg-turbo   \
  && apk add --virtual build-deps freetype-dev libpng-dev libjpeg-turbo-dev \
  && docker-php-ext-configure gd  --with-freetype=/usr/include/  --with-jpeg=/usr/include/ \
  && nproc=$(grep -c ^processor /proc/cpuinfo 2>/dev/null || 1) \
  && docker-php-ext-install -j${nproc} gd \
  && apk del build-deps

RUN apk add --no-cache libzip-dev && docker-php-ext-configure zip \
    && docker-php-ext-install zip

# Install nodejs
RUN apk add npm

# Upgrading Node
RUN npm cache clean -f
RUN npm install -g n

RUN apk add --no-cache icu-dev  && docker-php-ext-configure intl \
      && docker-php-ext-install intl pdo pdo_mysql sockets

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN chown -R www-data:www-data /var/www

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]


# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

RUN chown -R www-data:www-data /var/www

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
