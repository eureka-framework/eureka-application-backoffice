########################################################################################################################
# Base image with frankenphp (latest). For php 8.3, use tag "latest-php8.3"
########################################################################################################################
FROM dunglas/frankenphp:php8.4 AS frankenphp

#~ Upgrade the image & use specific php extensions
RUN apt update && apt upgrade -y && apt clean && apt install -y unzip && \
    install-php-extensions \
    curl \
    json \
    mbstring \
    memcache \
	opcache \
	pdo_mysql

#~ User and permissions to avoid running as root
ARG USER=appuser

#~ Handle composer for prod
ADD https://getcomposer.org/download/latest-2.x/composer.phar /usr/local/bin/composer

RUN \
    #~ Use "adduser -D ${USER}" for Alpine based distributions
    useradd ${USER}; \
    #~ Allow $USER to execute frankenphp binary
    chown -R ${USER}:${USER} /usr/local/bin/frankenphp; \
    #~ Allow rw for $USER on /data/caddy and /config/caddy
    chown -R ${USER}:${USER} /data/caddy && chown -R ${USER}:${USER} /config/caddy; \
    #~ Make composer executable
    chmod a+rx /usr/local/bin/composer;

USER ${USER}


########################################################################################################################
# Prod stage
########################################################################################################################
FROM frankenphp AS prod

#~ Copy code into container (using .dockerignore)
COPY --chown=${USER}:${USER} . /app

RUN cd /app/ && composer install --no-dev --optimize-autoloader --no-interaction --no-scripts


########################################################################################################################
# Dev stage
########################################################################################################################
FROM frankenphp AS dev

#~ FrankenPHP config env var
ENV CADDY_GLOBAL_OPTIONS="auto_https off"
ENV SERVER_NAME=":8080"

#~ No copy of the code, will be mounted from host
