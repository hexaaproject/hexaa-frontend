#!/bin/sh

# Exit on any errors
set -e

if [ ! -f /opt/hexaa-newui.deployed ]; then
    # dump css
    #su www-data -m -s /bin/sh -c "/usr/local/bin/php /opt/hexaa-newui/bin/console assetic:dump"
    php /opt/hexaa-newui/bin/console assetic:dump --env=prod --no-debug
fi

# Clear Symfony cache az startup
rm -rf /opt/hexaa-newui/var/cache/*

docker-php-entrypoint php-fpm
