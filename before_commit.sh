#!/bin/bash
docker exec -t project.local /var/www/project/phpcs.sh                                                                                                                                                          1 ↵
docker exec -t project.local sh -c '/var/www/project/vendor/bin/var-dump-check --symfony --exclude /var/www/project/vendor /var/www/project' 
