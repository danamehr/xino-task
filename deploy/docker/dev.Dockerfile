FROM ubuntu:22.04

ENV DEBIAN_FRONTEND noninteractive
ENV PHP_VERSION 8.2
ENV APP_KEY="AnExampleVeryLongApplicationKey!"

RUN apt-get update
RUN apt-get install -y software-properties-common curl
RUN apt-add-repository ppa:ondrej/nginx -y
RUN LC_ALL=C.UTF-8 apt-add-repository ppa:ondrej/php -y
RUN apt-get update && apt-get install -y \
    php${PHP_VERSION}-mcrypt \
    php${PHP_VERSION}-dom \
    php${PHP_VERSION}-curl \
    php${PHP_VERSION}-cli \
    php${PHP_VERSION}-mbstring \
    php${PHP_VERSION}-mysqli \
    php${PHP_VERSION}-pdo-mysql \
    php${PHP_VERSION}-redis \
    php${PHP_VERSION}-fpm \
    php${PHP_VERSION}-gd \
    php${PHP_VERSION}-zip \
    php${PHP_VERSION}-excimer \
    supervisor \
    zip unzip \
    git \
    nginx

RUN apt-get autoremove -y
RUN apt-get clean
RUN rm -rf /var/lib/apt/lists/*

RUN mkdir -p /run/php && chmod -R 755 /run/php
RUN mkdir -p /var/www/html

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ADD deploy/docker/nginx/nginx.conf /etc/nginx/nginx.conf
ADD deploy/docker/nginx/default.conf /etc/nginx/conf.d/default.conf
ADD deploy/docker/php/custom.ini /etc/php/${PHP_VERSION}/fpm/conf.d/custom.ini
ADD deploy/docker/php/www.conf /etc/php/${PHP_VERSION}/fpm/pool.d/www.conf
ADD deploy/docker/supervisord/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Add user for laravel application
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

WORKDIR /var/www/html

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
