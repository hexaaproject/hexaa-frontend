<?php
/**
 * Copyright 2016-2018 MTA SZTAKI ugyeletes@sztaki.hu
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

namespace AppBundle\EventListener;

use GuzzleHttp\Exception\ClientException;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Created by PhpStorm.
 * User: gyufi
 * Date: 2018. 02. 12.
 * Time: 10:07
 */
class GuzzleClientExceptionListener
{
    private $templating;
    private $router;

    /**
     * GuzzleClientExceptionListener constructor.
     *
     * @param TwigEngine      $templating
     * @param RouterInterface $router
     */
    public function __construct(TwigEngine $templating, RouterInterface $router)
    {
        $this->templating = $templating;
        $this->router = $router;
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        /** @var ClientException $exception */
        $exception = $event->getException();

        if ($exception instanceof ClientException) {
            $header = 'Error';
            $body = 'Some error...';
            $redirectUrl = $this->router->generate("homepage");
            $catched = false;

            switch ($exception->getCode()) {
                case 404:
                    $header = "Resource not found.";
                    $body = "The referred resource not found on backend.";
                    $catched = true;
                    break;
                case 403:
                    $header = "Access denied.";
                    $body = "You dont have access to the resource.";
                    $catched = true;
                    break;
            }

            if ($catched) {
                $response = new Response(
                    $this->templating->render(
                        'AppBundle:Errors:GuzzleClientException.html.twig',
                        array(
                            'header'      => $header,
                            'body'        => $body,
                            'redirectUrl' => $redirectUrl,
                        )
                    ),
                    Response::HTTP_OK
                );
                $event->setResponse($response);
            }
        }
    }
}
