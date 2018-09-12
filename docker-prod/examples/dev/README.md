# HEXAA development environment

1. Copy contents of examples/dev to a directory

2. Modify backend and/or frontend path to development directory in docker-compose.yml. E.g. hexaa_ui_source:/opt/hexaa-newui -> /your/dev/ui/path/app:/opt/hexaa-newui and/or hexaa_backend_source:/opt/hexaa-backend -> /your/dev/backend/path:/opt/hexaa-backend

    2.1.  Keep in mind, that the webserver in the container needs write acces to the var/cache, var/logs, var/session directories in the UI directory, and the app/cache, app/logs directories in the backend directory.
    
    2.2. Make sure you ran `composer install` in your development folder!

2. Modify user details in default-hexaa-fakeshib.conf

3. Start using docker-compose up -d.

4. Enter the backend container and set up the database:
 
  `sudo docker-compose exec backend bash`
  
  `cd /opt/hexaa-backend`
  
  `php app/console doctrine:schema:create`
  
  `rm -rf app/cache/* app/logs/*`
