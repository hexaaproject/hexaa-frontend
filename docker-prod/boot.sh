#!/bin/sh

# Exit on any errors
set -e

if [ ! -f /opt/hexaa-frontend.deployed ]; then
    # dump css
    #su www-data -m -s /bin/sh -c "/usr/local/bin/php /opt/hexaa-frontend/bin/console assetic:dump"
    php /opt/hexaa-frontend/bin/console assetic:dump --env=prod --no-debug
fi

# Clear Symfony cache az startup
rm -rf /opt/hexaa-frontend/var/cache/*

docker-php-entrypoint php-fpm
