<?php
/**
 * SAML 2.0 AA configuration for simpleSAMLphp.
 *
 */

$metadata['https://dev.hexaa.eu/aa'] = array(
        /*
         * The hostname of the server (VHOST) that will use this SAML entity.
         *
         * Can be '__DEFAULT__', to use this entry by default.
         */
        'host' => '__DEFAULT__',

        /* X.509 key and certificate. Relative to the cert directory. (default: /opt/simplesamlphp/cert) */
        'privatekey' => 'aa.server.key.pem',
        'certificate' => 'aa.server.cert.pem',

        'OrganizationName' => 'MTA SZTAKI',
        'OrganizationDisplayName' => 'MTA SZTAKI',
        'OrganizationURL' => 'sztaki.hu',

);
