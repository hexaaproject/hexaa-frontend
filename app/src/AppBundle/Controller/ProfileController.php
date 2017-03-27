<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("profile")
 */
class ProfileController extends Controller
{
    /**
     * @Route("/index")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $user = $this->get('principal')->getSelf();

        return $this->render(
            'AppBundle:Profile:index.html.twig',
            array(
                'propertiesbox' => $this->getPropertiesBox(),
                'main' => $user,
            )
        );
    }

    /**
     * @Route("/history")
     */
    public function historyAction()
    {
    }

    /**
     * @return array
     */
    private function getPropertiesBox()
    {
        $propertiesbox = array(
            "Name" => "display_name",
            "Email" => "email",
            "Federal ID" => "fedid",
        );

        return $propertiesbox;
    }
}
