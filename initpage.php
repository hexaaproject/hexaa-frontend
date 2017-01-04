<?php

/* 
 * Copyright 2016 Annamari.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Hexaa\Newui\User;
use Hexaa\Newui\Service\Authenticator;

if (!session_start()){
    // TODO error handling nicely
    trigger_error("Couldn't start session", E_ERROR);
}

$attribute_mapping = array(
	'eppn' => 'eduPersonPrincipalName',
	'displayName' => 'displayName',
	'mail' => 'mail',
	);
$attribute_errors = array();
foreach ($attribute_mapping as $key => $value) {
	if (! filter_input(INPUT_SERVER, $attribute_mapping[$key])) {
		$attribute_errors[]=$key;
	}
}
if (! empty($attribute_errors)) {
		throw new \Exception("Required attributes not found: ".implode(', ', $attribute_errors), 1);
}

$eppn = filter_input(INPUT_SERVER, $attribute_mapping["eppn"]);
$user = new User(
    filter_input(INPUT_SERVER, $attribute_mapping["eppn"]),
    filter_input(INPUT_SERVER, $attribute_mapping["displayName"]),
    filter_input(INPUT_SERVER, $attribute_mapping["mail"], FILTER_SANITIZE_EMAIL)
);

if (!$user->getEppn()){
    // TODO error handling nicely
    trigger_error("No eppn value found.", E_ERROR);
}

/** @noinspection PhpUndefinedVariableInspection */
$authenticator = new Authenticator($config, $user);
$client = new \GuzzleHttp\Client([
    'base_uri' => $config['backendUrl'],
    'headers' => ['X-HEXAA-AUTH' => $authenticator->getToken()]
]);
echo('TOKEN: ' . $authenticator->getToken() . '<br>');
$loader = new Twig_Loader_Filesystem(__DIR__ . '/views/');
$twig = new Twig_Environment($loader);
