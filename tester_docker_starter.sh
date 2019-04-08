#!/bin/bash
docker rm db -f 
docker rm memcached -f 
docker rm hexaa-backend -f 
docker rm grid -f 
docker rm hexaa-test-data-manager -f
docker rm project.local -f
docker network rm hexaa-test

docker network create hexaa-test
docker run --network=hexaa-test -d --name db -e MYSQL_ALLOW_EMPTY_PASSWORD=true -e MYSQL_USER=someuser -e MYSQL_PASSWORD=somepass -e MYSQL_DATABASE=hexaa mysql
docker run --network=hexaa-test -d --name memcached memcached
docker run --network=hexaa-test -d --name grid elgalu/selenium
docker run --network=hexaa-test -d --name project.local hexaa/hexaa-frontend
docker run --network=hexaa-test -d --name hexaa-test-data-manager hexaaproject/hexaa-test-data-manager:for-dev
docker run --network=hexaa-test dmfenton/wait db:3306 -t 120
docker run --network=hexaa-test -d --name hexaa-backend hexaaproject/hexaa-backend:for-dev
docker run --network=hexaa-test dmfenton/wait hexaa-backend:80 -t 120
docker run --network=hexaa-test dmfenton/wait project.local:443 -t 120

docker exec -t project.local /var/www/project/test.sh /var/www/project