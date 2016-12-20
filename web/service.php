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

include_once '../initpage.php';

try {
    $serviceid = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $menu = filter_input(INPUT_GET,'menu');
    if (!$menu) {
        $menu = "main";
    }
    if ($serviceid) {
        $service = \Hexaa\Newui\Model\Service::get($client, $serviceid);
    }
    $organizations = \Hexaa\Newui\Model\Organization::cget($client);
 
    $services = \Hexaa\Newui\Model\Service::cget($client);
    
} catch (ClientException $e) {
    $this->token = null;
    $templateerror = $twig->loadTemplate('error.html.twig');
    echo $templateerror->render(array('clientexception' => $e));
} catch (ServerException $e) {
    $this->token = null;
    $templateerror = $twig->loadTemplate('error.html.twig');
    echo $templateerror->render(array('serverexception' => $e));
} finally {
    //?
}

$template = $twig->loadTemplate('servicemain.html.twig');

echo $template->render(array('user' => $user, 'service' => $service, 'organizations' => $organizations, 'services' => $services, 'menu' => $menu, ));