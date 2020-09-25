FROM php:7.2-fpm
# Imagen padre de la que partimos y a partir de la que crearemos los ficheros

# Copy composer.lock and composer.json //estoy copiando los ficheros ya creados y me los metes en el directorio var dentro del contenedor
COPY composer.lock composer.json /var/www/

# Set working directory //cambiame de directorio dentro del contenedor y todos los comandos que te diré a partir de ahora van dentro de este contenedor
WORKDIR /var/www

# Install dependencies //mi imagen necesita ciertas dependencias no las tiene instaladas, le pido que me instale todas estas librerias que voy a necesitar
RUN apt-get update && apt-get install -y \
    build-essential \
    mariadb-client \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install extensions
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl
RUN docker-php-ext-configure gd --with-gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ --with-png-dir=/usr/include/
RUN docker-php-ext-install gd

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Add user for laravel application
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# Copy existing application directory contents //copio toddo lo que hay dentro y lo traslado dentro del contenedor
COPY . /var/www

# Copy existing application directory permissions
COPY --chown=www:www . /var/www

# Change current user to www
USER www

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
