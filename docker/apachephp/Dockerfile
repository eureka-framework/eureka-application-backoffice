########################################################################################################################
# Base apache / php image
########################################################################################################################
FROM php-apache:8.3-1-jammy AS apachephp

#~ Some ARGs config
ARG PHP_VERSION=8.3
ARG USER_ID=33
ARG GROUP_ID=33
ARG USER_NAME="www-data"
ARG PATH_APP="/app"

#~ Upgrade the image & use specific php extensions
RUN apt update \
 && apt install -y \
        php${PHP_VERSION}-common \
        php${PHP_VERSION}-curl \
        php${PHP_VERSION}-mbstring \
        php${PHP_VERSION}-memcache \
        php${PHP_VERSION}-mysql \
        php${PHP_VERSION}-ndl \
        php${PHP_VERSION}-xml \
        deezer-php${PHP_VERSION}-rdkafka \
        jq \
        unzip \
 && apt clean \
 && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*


#~ Handle composer for prod
ADD https://getcomposer.org/download/latest-2.x/composer.phar /usr/local/bin/composer

RUN chmod a+rx /usr/local/bin/composer

#~ Copy vhost config
COPY etc/vhost.conf /etc/apache2/sites-available/deezer.conf

#~ Enable some apache mods + set
RUN a2enmod rewrite \
 && a2enmod headers \
 && rm -f /etc/apache2/sites-enabled/000-default.conf \
 && ln -s /etc/apache2/sites-available/deezer.conf /etc/apache2/sites-enabled/000-deezer.conf \
 && install -d -m 0755 -o ${USER_NAME} -g ${USER_NAME} ${PATH_APP} \
 && chmod +x /root/  \
 && chown ${USER_NAME}: /root/apache.sh \
 && chown -R ${USER_NAME}: /var/log/apache2 /var/run/apache2 \
 && sed -i "s/var\/www/app/g" /etc/apache2/apache2.conf \
 && sed -i "s/Listen 80/Listen 8080/g" /etc/apache2/ports.conf \
 && sed -i "s/:80/:8080/g" /etc/apache2/sites-available/*.conf


########################################################################################################################
# Stage "dev"
########################################################################################################################
FROM apachephp AS dev
# ~ No copy of the code, will be mounted from host


########################################################################################################################
# Stage "prod"
########################################################################################################################
FROM apachephp AS prod
#~ Copy code into container
COPY . ${PATH_APP}

WORKDIR ${PATH_APP}

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts \
 && chown --silent --no-dereference --recursive --from=0:0 ${USER_NAME}:${USER_NAME} . \
 && chmod -R 0755 var/cache

#~ Use app user
USER ${USER_NAME}
