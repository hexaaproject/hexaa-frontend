#!/bin/sh

# Exit on any errors
set -e

# Set some sensible default parameters

# you can generate a secret using:
# tr -c -d '0123456789abcdefghijklmnopqrstuvwxyz' </dev/urandom | dd bs=32 count=1 2>/dev/null;echo
HEXAA_FRONTEND_SECRET=${HEXAA_FRONTEND_SECRET:-"ThisTokenIsNotSoSecretChangeIt"}
HEXAA_FRONTEND_SHIB_USERNAME_ATTRIBUTE=${HEXAA_FRONTEND_SHIB_USERNAME_ATTRIBUTE:-"eduPersonPrincipalName"}
HEXAA_FRONTEND_SHIB_MODULE_ATTRIBUTE=${HEXAA_FRONTEND_SHIB_MODULE_ATTRIBUTE:-"HTTP_SHIB_APPLICATION_ID"}
HEXAA_FRONTEND_SHIB_ATTRIBUTE_MAP=${HEXAA_FRONTEND_SHIB_ATTRIBUTE_MAP:-"\n        eppn: eduPersonPrincipalName\n        displayName: displayName\n        email: mail"}
HEXAA_FRONTEND_SHIB_LOGOUT_RETURN_PATH=${HEXAA_FRONTEND_SHIB_LOGOUT_RETURN_PATH:-"homepage"}
# note: add "/" at the end of the URL!
HEXAA_FRONTEND_API_BASE_URL=${HEXAA_FRONTEND_API_BASE_URL:-"http://hexaa-backend/api/"}
HEXAA_FRONTEND_API_PERMISSION_PREFIX=${HEXAA_FRONTEND_API_PERMISSION_PREFIX:-"some:entitlement:prefix:hexaa"}
HEXAA_FRONTEND_API_SCOPED_KEY=${HEXAA_FRONTEND_API_SCOPED_KEY:-"InsertSomeSecretHere"}
HEXAA_FRONTEND_INVITATION_CONFIG=${HEXAA_FRONTEND_INVITATION_CONFIG:-"\n        subject: \"[HEXAA] invitation\"\n        from: \"no_reply@hexaa.eduid.hu\"\n        reply-to: \"no_reply@hexaa.eduid.hu\"\n        footer: \"sincerely: hexaa.eduid.hu\""}
# Mailer parameters
HEXAA_FRONTEND_MAILER_TRANSPORT=${HEXAA_FRONTEND_MAILER_TRANSPORT:-"smtp"}
HEXAA_FRONTEND_MAILER_HOST=${HEXAA_FRONTEND_MAILER_HOST:-"smtp"}
HEXAA_FRONTEND_MAILER_PORT=${HEXAA_FRONTEND_MAILER_PORT:-"~"}
HEXAA_FRONTEND_MAILER_USER=${HEXAA_FRONTEND_MAILER_USER:-"~"}
HEXAA_FRONTEND_MAILER_PASSWORD=${HEXAA_FRONTEND_MAILER_PASSWORD:-"~"}

HEXAA_FRONTEND_LOG_TO_STDERR=${HEXAA_FRONTEND_LOG_TO_STDERR:-"true"}

# Copy alternative logging config and clear cache IF configured to do so
if [ "$HEXAA_FRONTEND_LOG_TO_STDERR" = "true" ]; then
    cp /root/config_prod.yml /opt/hexaa-newui/app/config/config_prod.yml
fi

# Write parameters.yml
HEXAA_FRONTEND_PARAMETERS_YML=$(cat <<EOF
parameters:
    secret:                       $HEXAA_FRONTEND_SECRET

    shib_auth_username_attribute: $HEXAA_FRONTEND_SHIB_USERNAME_ATTRIBUTE
    shib_auth_module_attribute:   $HEXAA_FRONTEND_SHIB_MODULE_ATTRIBUTE
    shib_attribute_map:${HEXAA_FRONTEND_SHIB_ATTRIBUTE_MAP}
    shib_auth_logout_return_path: $HEXAA_FRONTEND_SHIB_LOGOUT_RETURN_PATH
    hexaa_base_uri:               $HEXAA_FRONTEND_API_BASE_URL
    hexaa_permissionprefix:       '$HEXAA_FRONTEND_API_PERMISSION_PREFIX'
    hexaa_scoped_key:             $HEXAA_FRONTEND_API_SCOPED_KEY
    invitation_config:${HEXAA_FRONTEND_INVITATION_CONFIG}
    
    mailer_transport:  $HEXAA_FRONTEND_MAILER_TRANSPORT
    mailer_host:       $HEXAA_FRONTEND_MAILER_HOST
    mailer_port:       $HEXAA_FRONTEND_MAILER_PORT
    mailer_user:       $HEXAA_FRONTEND_MAILER_USER
    mailer_password:   $HEXAA_FRONTEND_MAILER_PASSWORD

EOF
)

echo "$HEXAA_FRONTEND_PARAMETERS_YML" > /opt/hexaa-newui/app/config/parameters.yml

if [ ! -f /opt/hexaa-newui.deployed ]; then
    # dump css
    #su www-data -m -s /bin/sh -c "/usr/local/bin/php /opt/hexaa-newui/bin/console assetic:dump"
    php /opt/hexaa-newui/bin/console assetic:dump --env=prod --no-debug
fi

# Clear Symfony cache az startup
rm -rf /opt/hexaa-newui/var/cache/*

docker-php-entrypoint php-fpm
