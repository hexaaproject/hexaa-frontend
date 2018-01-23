FROM php:7.2.1-fpm-stretch
LABEL MAINTAINER Balázs SOLTÉSZ <solazs@sztaki.hu>

# Install some dependencies
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
                       wget \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY ./app/. /opt/hexaa-newui

# Install composer
COPY docker-prod/composer_downloader.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/composer_downloader.sh \
    && cd /usr/local/bin/ \
    && /usr/local/bin/composer_downloader.sh \
    && mv /usr/local/bin/composer.phar /usr/local/bin/composer \
    && chmod +x /usr/local/bin/composer
    
# Install dependencies
RUN cd /opt/hexaa-newui \
    && composer install -o \
    && rm -rf /var/cache/* \
    && cd /var/www && rm -r html && ln -s html /opt/hexaa-newui/web 

VOLUME /opt/hexaa-newui

COPY docker-prod/boot.sh /usr/local/bin/

CMD ["/usr/local/bin/boot.sh"]
