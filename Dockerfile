FROM szabogyula/saml-webapp-runner:ubuntu16.04

#ADD docker/run.sh /run.sh
#ADD docker/test.sh /test.sh

# build application
ADD app /var/www/project
RUN cd /var/www/project \
    && apt-get update \
    && DEBIAN_FRONTEND=noninteractive apt-get -y upgrade \
    && DEBIAN_FRONTEND=noninteractive apt-get install -y php-mbstring unzip \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-interaction \
    && bin/console assetic:dump \
    && chown -R www-data var
