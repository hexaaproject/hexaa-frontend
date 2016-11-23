<?php

/* 
 * Copyright 2016 Mihály Héder <mihaly.heder@sztaki.mta.hu>.
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

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config.php';

use Hexaa\Newui\User;
use Hexaa\Newui\Service\Authenticator;

if (!session_start()){
    // TODO error handling nicely
    trigger_error("Couldn't start session", E_ERROR);
}



$eppn = filter_input(INPUT_SERVER,"eppn");
$user = new User(
    filter_input(INPUT_SERVER,"eppn"),
    filter_input(INPUT_SERVER,"displayName"),
    filter_input(INPUT_SERVER,"mail",FILTER_SANITIZE_EMAIL)
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

$loader = new Twig_Loader_Filesystem(__DIR__ . '/../views/');
$twig = new Twig_Environment($loader);

$organizations = \Hexaa\Newui\Model\Organization::cget($client);
$services = \Hexaa\Newui\Model\Service::cget($client);

$template = $twig->loadTemplate('startpage.html.twig');


echo $template->render(array('user' => $user, 'organizations' => $organizations));