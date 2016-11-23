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
    $organizations = \Hexaa\Newui\Model\Organization::cget($client);
    $services = \Hexaa\Newui\Model\Service::cget($client);
} catch (ClientException $e) {
    
    $this->token = null;
    $templateerror = $twig->loadTemplate('error.html.twig');
    echo $templateerror->render(array('clientexception'=>$e));
    
   // echo('<br>___.--===(ClientException)===--.___<br>');
   // echo('Message: ' . $e->getMessage() . '<br>');
   //  echo('Call: ' . $e->getRequest()->getUri() . '<br>');
   // echo('Request method: ' . $e->getRequest()->getMethod() . ', body: <br>');
   // echo($e->getRequest()->getBody() . '<br>');
   // echo('Response code: ' . $e->getResponse()->getStatusCode() . ', body: <br>');
   // echo($e->getResponse()->getBody() . '<br>');
} catch (ServerException $e) {
    $this->token = null;
    $templateerror = $twig->loadTemplate('error.html.twig');
    echo $templateerror->render(array('serverexception'=>$e));
    
    //echo('<br>___.--===(ServerException)===--.___<br>');
    //echo('Message: ' . $e->getMessage() . '<br>');
    //echo('Call: ' . $e->getRequest()->getUri() . '<br>');
    //echo('Request method: ' . $e->getRequest()->getMethod() . ', body: <br>');
    //echo($e->getRequest()->getBody() . '<br>');
    //echo('Response code: ' . $e->getResponse()->getStatusCode() . ', body: <br>');
    //echo($e->getResponse()->getBody() . '<br>');
} finally {
    if (!isset($organizations)){
        $organizations = [];
    }
    if (!isset($services)){
        $services = [];
    }
}

$template = $twig->loadTemplate('startpage.html.twig');


echo $template->render(array('user' => $user, 'organizations' => $organizations, 'services'=>$services));