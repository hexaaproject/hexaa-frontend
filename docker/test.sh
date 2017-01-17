#!/bin/sh
cd /var/www/project
# populate test datas
#app/console h:d:f:l -n
echo "check environment"
ping -c 5 grid
ping -c 5 hexaa-backend
/var/www/project/test.sh /var/www/project
