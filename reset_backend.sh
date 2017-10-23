#!/bin/bash
docker exec -ti hexaa-backend php ../app/console doctrine:schema:drop -f
docker exec -ti hexaa-backend php ../app/console doctrine:schema:create 
docker exec -ti project.local /var/www/project/vendor/bin/behat -c /var/www/project/behat.yml --tags setup
docker exec -ti project.local rm -rf /tmp/symfony/cache/*
docker exec -ti project.local /var/www/project/bin/console cache:warmup --env prod
docker exec -ti project.local chown -R www-data.www-data  /tmp/symfony/cache

