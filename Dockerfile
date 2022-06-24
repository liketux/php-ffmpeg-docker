FROM php:7.4-apache

WORKDIR /var/www/html/

RUN apt-get update
RUN apt-get install -y git build-essential cmake pkg-config ffmpeg
RUN rm -rf /var/lib/apt/lists/*

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions gd bcmath intl mcrypt zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY ./app/ ./

RUN export COMPOSER_ALLOW_SUPERUSER=1 && \
	composer install --no-dev --ignore-platform-reqs --optimize-autoloader
