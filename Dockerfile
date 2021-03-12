FROM phpdockerio/php72-fpm:latest

ARG DEBIAN_FRONTEND=noninteractive

RUN apt-get update \
    && apt-get -y --no-install-recommends install php7.2-sqlite3 php7.2-xsl php-yaml php-pcov \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www