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
include_once '../initpage.php';


try {
    $organizationid = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $menu = filter_input(INPUT_GET,'menu');
    if (!$menu) {
        $menu = "main";
    }
    if ($organizationid) {
        $organization = \Hexaa\Newui\Model\Organization::get($client, $organizationid);
        $droleid=$organization['default_role_id'];
        $verbose="expanded";
        $roles=\Hexaa\Newui\Model\Organization::rget($client, $organizationid, $verbose);
        $name='';
        foreach ($roles as $value){
            if($value['id']==$droleid){
                $name=$value['name'];
            }
        }
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
    if (!isset($organizations)){
        $organizations = [];
    }
    if (!isset($services)){
        $services = [];
    }
}

$template = $twig->loadTemplate('organizationmain.html.twig');


echo $template->render(array('user' => $user, 'organization' => $organization, 'organizations' => $organizations, 'services' => $services, 'menu' => $menu, 'drolename' => $name, 'roles'=>$roles, 'principals'=>$principals));
