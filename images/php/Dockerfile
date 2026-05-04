FROM php:8.4-fpm

# Установка зависимостей системы
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    zip \
    unzip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Конфигурация и установка PHP расширений
RUN docker-php-ext-configure gd --enable-gd --with-jpeg --with-freetype && \
    docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

# Установка PHP конфигурации (timezone, upload limits)
RUN echo "date.timezone=Europe/Moscow" >> /usr/local/etc/php/conf.d/timezone.ini && \
    echo "upload_max_filesize=1000M" >> /usr/local/etc/php/conf.d/uploads.ini && \
    echo "post_max_size=1000M" >> /usr/local/etc/php/conf.d/uploads.ini

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Установка рабочей директории
WORKDIR /var/www/html

# Открытие порта для PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]
