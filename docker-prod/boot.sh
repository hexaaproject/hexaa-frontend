#!/bin/sh

# Exit on any errors
set -e

# Set some sensible default parameters

# you can generate a secret using:
# tr -c -d '0123456789abcdefghijklmnopqrstuvwxyz' </dev/urandom | dd bs=32 count=1 2>/dev/null;echo
HEXAA_FRONTEND_SECRET=${HEXAA_FRONTEND_SECRET:-"ThisTokenIsNotSoSecretChangeIt"}
HEXAA_FRONTEND_SHIB_USERNAME_ATTRIBUTE=${HEXAA_FRONTEND_SHIB_USERNAME_ATTRIBUTE:-"eduPersonPrincipalName"}
HEXAA_FRONTEND_SHIB_MODULE_ATTRIBUTE=${HEXAA_FRONTEND_SHIB_MODULE_ATTRIBUTE:-"HTTP_SHIB_APPLICATION_ID"}
HEXAA_FRONTEND_SHIB_ATTRIBUTE_MAP=${HEXAA_FRONTEND_SHIB_ATTRIBUTE_MAP:-"
        eppn: eduPersonPrincipalName
        displayName: displayName
        email: mail"}
HEXAA_FRONTEND_SHIB_LOGOUT_RETURN_PATH=${HEXAA_FRONTEND_SHIB_LOGOUT_RETURN_PATH:-"homepage"}
# note: add "/" at the end of the URL!
HEXAA_FRONTEND_API_BASE_URL=${HEXAA_FRONTEND_BASE_URL:-"http://hexaa-backend/api/"}
HEXAA_FRONTEND_API_PERMISSION_PREFIX=${HEXAA_FRONTEND_API_PERMISSION_PREFIX:-"some:entitlement:prefix:hexaa"}
HEXAA_FRONTEND_API_SCOPED_KEY=${HEXAA_FRONTEND_API_SCOPED_KEY:-"InsertSomeSecretHere"}
HEXAA_FRONTEND_INVITATION_CONFIG=${HEXAA_FRONTEND_INVITATION_CONFIG:-"
        subject: \"[HEXAA] invitation\"
        from: \"no_reply@hexaa.eduid.hu\"
        reply-to: \"no_reply@hexaa.eduid.hu\"
        footer: \"sincerely: hexaa.eduid.hu\""}
# Mailer parameters
HEXAA_FRONTEND_MAILER_TRANSPORT=${HEXAA_FRONTEND_MAILER_TRANSPORT:-"smtp"}
HEXAA_FRONTEND_MAILER_HOST=${HEXAA_FRONTEND_MAILER_HOST:-"smtp"}
HEXAA_FRONTEND_MAILER_PORT=${HEXAA_FRONTEND_MAILER_PORT:-"~"}
HEXAA_FRONTEND_MAILER_USER=${HEXAA_FRONTEND_MAILER_USER:-"~"}
HEXAA_FRONTEND_MAILER_PASSWORD=${HEXAA_FRONTEND_MAILER_PASSWORD:-"~"}

# Write parameters.yml
cat /opt/hexaa-newui/app/config/parameters.yml <<EOF
parameters:
    secret:                       $HEXAA_FRONTEND_SECRET

    shib_auth_username_attribute: $HEXAA_FRONTEND_SHIB_USERNAME_ATTRIBUTE
    shib_auth_module_attribute:   $HEXAA_FRONTEND_SHIB_MODULE_ATTRIBUTE
    shib_attribute_map:$HEXAA_FRONTEND_SHIB_ATTRIBUTE_MAP
    shib_auth_logout_return_path: $HEXAA_FRONTEND_SHIB_LOGOUT_RETURN_PATH
    hexaa_base_uri:               $HEXAA_FRONTEND_API_BASE_URL
    hexaa_permissionprefix:       '$HEXAA_FRONTEND_API_PERMISSION_PREFIX'
    hexaa_scoped_key:             $HEXAA_FRONTEND_API_SCOPED_KEY
    invitation_config:$HEXAA_FRONTEND_INVITATION_CONFIG
    
    mailer_transport:  $HEXAA_FRONTEND_MAILER_TRANSPORT
    mailer_host:       $HEXAA_FRONTEND_MAILER_HOST
    mailer_port:       $HEXAA_FRONTEND_MAILER_PORT
    mailer_user:       $HEXAA_FRONTEND_MAILER_USER
    mailer_password:   $HEXAA_FRONTEND_MAILER_PASSWORD

EOF
