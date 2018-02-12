<?php

namespace AppBundle\EventListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

/**
 * Created by PhpStorm.
 * User: gyufi
 * Date: 2018. 02. 12.
 * Time: 10:07
 */
class GuzzleClientExceptionListener
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $response = new Response();

        $event->setResponse($response);
    }
}