<?php

namespace AppBundle\Controller;

use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginListener
{
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();
        /*$event->getRequest()->getSession()->set('hexaaAdmin', 'false');*/
    }
}