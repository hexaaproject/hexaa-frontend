# HEXAA development environment

1. Copy contents of examples/dev to a directory

2. Modify backend and/or frontend path to development directory in docker-compose.yml. E.g. hexaa_ui_source:/opt/hexaa-newui -> /your/dev/ui/path/:/opt/hexaa-newui

2. Modify user details in default-hexaa-fakeshib.conf

3. Start using docker-compose up -d.

4. Enter the backend container and set up the database:
 
  `sudo docker-compose exec backend bash`
  
  `cd /opt/hexaa-backend`
  
  `php app/console doctrine:schema:create
  
  rm -rf app/cache/* app/logs/*
