FROM php:7.2.1-fpm-stretch
LABEL MAINTAINER Balázs SOLTÉSZ <solazs@sztaki.hu>

# Install some dependencies
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
                       wget \
                       unzip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Copy custom php.ini
# COPY docker-prod/php.ini /usr/local/etc/php/

# Copy source to container
COPY docker-prod/config_prod.yml /root/
COPY app /opt/hexaa-newui

# Install composer
COPY docker-prod/composer_downloader.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/composer_downloader.sh \
    && sync \
    && cd /usr/local/bin/ \
    && /usr/local/bin/composer_downloader.sh \
    && mv /usr/local/bin/composer.phar /usr/local/bin/composer \
    && chmod +x /usr/local/bin/composer
    
# Install dependencies, and other stuff
RUN cd /opt/hexaa-newui \
    && composer install -o \
    && rm -rf var/cache/* \
    && chown root:www-data /opt/hexaa-newui -R \
    && chmod g+r /opt/hexaa-newui -R \
    && chmod g+w /opt/hexaa-newui/var/cache -R\
    && chmod g+w /opt/hexaa-newui/var/logs -R \
    && mkdir /var/lib/php5 && chown www-data /var/lib/php5 \
    && mkdir /var/run/php \
    && touch /var/run/php/php-fpm-frontend.sock \
    && chown www-data /var/run/php/php-fpm-frontend.sock

VOLUME /opt/hexaa-newui /var/run/php

COPY docker-prod/boot.sh /usr/local/bin/

CMD ["/usr/local/bin/boot.sh"]
