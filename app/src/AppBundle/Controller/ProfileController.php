<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Model\Organization;
use AppBundle\Model\Service;
/**
 * @Route("profile")
 */
class ProfileController extends Controller
{
    /**
     * @Route("/index")
     */
    public function indexAction()
    {
        $client = $this->getUser()->getClient();
        $user = $this->get('principal')->principalinfo($client);
        return $this->render(
            'AppBundle:Profile:index.html.twig',
                array(
                    'propertiesbox' => $this->getPropertiesBox(),
                    'main' => $user
                )
        );
    }
    
     /**
     * @Route("/history")
     */
    public function historyAction(){
        
    }
    
    private function getPropertiesBox() {
        $propertiesbox = array(
            "Name" => "display_name",
            "Email" => "email",
            "Federal ID" => "fedid",
        );

        return $propertiesbox;
    }
    
}
