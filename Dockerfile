FROM szabogyula/saml-webapp-runner:ubuntu16.04

# build application
ADD app /var/www/project
RUN cd /var/www/project \
    && apt-get install -y php-mbstring unzip \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && rm -rf /app/app/cache/* \
    && php /usr/local/bin/composer install --no-interaction

# install the runner
ADD docker/run.sh /run.sh
ADD docker/test.sh /test.sh