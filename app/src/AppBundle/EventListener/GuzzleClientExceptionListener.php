<?php

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


    public function __construct(TwigEngine $templating, RouterInterface $router)
    {
        $this->templating = $templating;
        $this->router = $router;
    }

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