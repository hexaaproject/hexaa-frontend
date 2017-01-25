#!/bin/bash
docker rm db -f 
docker rm memcached -f 
docker rm hexaa-backend -f 
docker rm grid -f 
docker rm hexaa-test-data-manager -f
docker rm project.local -f

docker run -d --name db -e MYSQL_ALLOW_EMPTY_PASSWORD=true -e MYSQL_USER=someuser -e MYSQL_PASSWORD=somepass -e MYSQL_DATABASE=hexaa mysql
docker run -d --name memcached memcached
docker run -d --name grid -p 5900:25900 --add-host project.local:172.17.0.1 elgalu/selenium
docker run -d --name hexaa-backend --link db:db --link memcached:memcached hexaaproject/hexaa-backend:for-dev
docker run -d --name hexaa-test-data-manager --link db:db --link hexaa-backend:hexaa-backend hexaaproject/hexaa-test-data-manager:for-dev
docker run -d --name project.local -p 80:80 -p 443:443 -p 8080:8080 --add-host project.local:127.0.0.1 --link grid:grid --link hexaa-test-data-manager:hexaa-test-data-manager --link hexaa-backend:hexaa-backend solazs/hexaa-newui

sleep 18
docker exec -t project.local /var/www/project/test.sh /var/www/project
