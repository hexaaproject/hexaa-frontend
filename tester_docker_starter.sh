#!/bin/bash
docker rm db
docker rm memcached
docker rm hexaa-backend
docker rm grid
docker rm hexaa-test-data-manager

docker run -d --name db -e MYSQL_ALLOW_EMPTY_PASSWORD=true -e MYSQL_USER=someuser -e MYSQL_PASSWORD=somepass -e MYSQL_DATABASE=hexaa mysql
docker run -d --name memcached memcached
docker run -d --name hexaa-backend --link db:db --link memcached:memcached hexaaproject/hexaa-backend:for-dev
docker run -d --name grid elgalu/selenium
docker run -d --name hexaa-test-data-manager --link hexaa-backend:hexaa-backend hexaaproject/hexaa-test-data-manager:for-dev
docker run --rm --link db:db --link grid:grid --link hexaa-test-data-manager:hexaa-test-data-manager -t solazs/hexaa-newui /var/www/project/test.sh /var/www/project
