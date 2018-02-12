<?php

namespace AppBundle\EventListener;
use Symfony\Bundle\TwigBundle\TwigEngine;
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
    private $templating;

    public function __construct(TwigEngine $templating)
    {
        $this->templating = $templating;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        $response = new Response($this->templating->render('AppBundle:Errors:GuzzleClientException.html.twig'));
        $event->setResponse($response);
    }
}