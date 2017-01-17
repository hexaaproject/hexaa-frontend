#!/bin/bash

/app/app/console doctrine:database:create --if-not-exists
/app/app/console doctrine:schema:update --force
app/console app:migrate
app/console hautelook_alice:doctrine:fixtures:load --no-interaction

# Futtatáskor --env "VAULT_PASS=***" formában add meg az ansible vault jelszót
# # if [ -z "$VAULT_PASS" ]; then
# #     echo "Need to set VAULT_PASS"
# #     exit 1
# # fi
# for i in /etc/ssl/private/server.pem /etc/shibboleth/sp-key.pem /app/vendor/simplesamlphp/simplesamlphp/cert/server.pem /app/vendor/simplesamlphp/simplesamlphp/config/config.php
# do
#   openssl des3 -d -k $VAULT_PASS -in $i.enc -out $i
# done

chown www-data:www-data /app -R
rm -rf /var/www/html
ln -s /app/web /var/www/html

sed -i "s/AllowOverride None/AllowOverride All/g" /etc/apache2/apache2.conf
# DocumentRoot /var/www/html/web
# sed -i "s#DocumentRoot /var/www/html#DocumentRoot /var/www/html/web#g" /etc/apache2/sites-available/000-default.conf

sed -i "s/gmap_api_key:.*/gmap_api_key: $GMAP_API_KEY/g" app/config/parameters.yml

source /etc/apache2/envvars
tail -F /var/log/apache2/*  /app/app/logs/* &
exec apache2 -D FOREGROUND