#!/bin/bash
docker rm db -f 
docker rm memcached -f 
docker rm hexaa-backend -f 
docker rm grid -f 
docker rm hexaa-test-data-manager -f
docker rm project.local -f
docker network rm hexaa-test


docker network create hexaa-test
docker run -d --name db --network=hexaa-test -e MYSQL_ALLOW_EMPTY_PASSWORD=true -e MYSQL_USER=someuser -e MYSQL_PASSWORD=somepass -e MYSQL_DATABASE=hexaa mysql
docker run -d --name memcached --network=hexaa-test memcached
docker run -d --name grid --network=hexaa-test elgalu/selenium
docker run -d --name hexaa-backend --network=hexaa-test hexaaproject/hexaa-backend:for-dev
docker run -d --name hexaa-test-data-manager --network=hexaa-test hexaaproject/hexaa-test-data-manager:for-dev
docker run -d --name project.local --network=hexaa-test solazs/hexaa-newui

docker logs hexaa-backend

docker run --network=hexaa-test dmfenton/wait hexaa-backend:80 -t 60

docker exec -t project.local /var/www/project/test.sh /var/www/project
